function displayInitialMapSelectionScreen() {
  hide(GamePanel.gameOuterDiv);
  show(SelectionPanel.selectionDiv);
  hide(SelectionPanel.nameForm)
  SelectionPanel.replaceWithNewTbody();
  prevResults.forEach(SelectionPanel.putNewRecordTR);
  // console.log(savedGame);
  savedGame = permaLoadJSON("savedGame")[0];
  if (savedGame) {
    show(SelectionPanel.resumeBtn);
  } else {
    hide(SelectionPanel.resumeBtn);
  }
}

function displayInitialGameScreen() {
  hide(SelectionPanel.selectionDiv);
  show(GamePanel.gameOuterDiv);
  GamePanel.updateControlPanelInfo(
    game.getPlayerName(), 
    game.getMapName(), 
    game.getElapseTime()
  );
  GamePanel.drawMap(game.map);
  hide(document.querySelector("#save-message"));
  if (game.checkSolution()) {
    GamePanel.updateSuccessStatus(true)
  } else {
    // GamePanel.updateControlPanelButton(false);
    GamePanel.updateSuccessStatus(false)
  }
}

//! ------------ main --------------
let savedGame = permaLoadJSON("savedGame")[0];
let game = new BulbPlacementGame(
  EASYMAP,
  EASYMAP_SOLUTION,
  0,
  "Easy",
  "nobody"
);

displayInitialMapSelectionScreen();


//! --------------- EVENT HANLE --------------------
const saveBtn = document.querySelector("#save");
const mainMenuBtn = document.querySelector("#main_menu");
const restartBtn = document.querySelector("#restart");
const submitInput = document.querySelector("#submit");

//! Handle Buttons ---------------------------------
SelectionPanel.resumeBtn.addEventListener("click", () => {
  
  game = new BulbPlacementGame(
    savedGame.map,
    savedGame.solution,
    savedGame.timeElapse,
    savedGame.mapName,
    savedGame.playerName
  );
  // console.log(game)
  displayInitialGameScreen();
});

saveBtn.addEventListener("click", (event) => {
  permaSaveJSON("savedGame", [game.toJson()]);
  show(document.querySelector("#save-message"));
  show(SelectionPanel.resumeBtn);
});

// restartBtn.classList.add("hidden");
restartBtn.addEventListener("click", (event) => {
  game.reset();
  displayInitialGameScreen()
});

mainMenuBtn.addEventListener("click", (event) => {
  displayInitialMapSelectionScreen();
});

submitInput.addEventListener("click", (event) => {
  event.preventDefault();
  game.reset();
  let inputPlayerName = document.querySelector("#name").value;
  document.querySelector("#name").value = null;
  game.setPlayerName(inputPlayerName);
  displayInitialGameScreen();
});

// ! Handle normal map select btns ------------------
delegate(
  document.querySelector("#defa-map-select-btns"),
  "click",
  "button",
  onClickMapSelectionBtn
);
function onClickMapSelectionBtn(event, targetElement) {
  if (targetElement.dataset.mname == "Saved") {
    return;
  }
  const settings = SelectionPanel.getMapByBtn(targetElement.dataset.mname);
  // console.log(settings);
  game = new BulbPlacementGame(
    settings.map,
    settings.solution,
    settings.timeElapse,
    settings.mname,
    settings.pname
  );
  // console.log(game.toJson());
  show(SelectionPanel.nameForm);
}

//! Handle user's bulb placement -----------------------------
delegate(GamePanel.gameDiv, "click", "td", placeBulb);

function placeBulb(event, tile) {
  if (tile.classList.contains("BlackTile")) {
    return; // Do noting
  }
  if (tile.classList.contains("BulbTile")) {
    // Remove bulb from list
    game.removeBulb({ row: tile.dataset.rowIndex, col: tile.dataset.colIndex });
    // Redraw everything
    GamePanel.drawMap(game.getMap());
  } else {
    // if it is an unclicked white tile
    // Add Bulb to list
    let newBulb = { row: tile.dataset.rowIndex, col: tile.dataset.colIndex };
    game.addBulb(newBulb);
    //TODO Beautiful Draw
    GamePanel.beautifulDraw(tile, game.getMap());
  }
  if (game.checkSolution()) {
    // GamePanel.updateControlPanelButton(true);
    GamePanel.updateSuccessStatus(true)
    goodResult = {
      name: game.getPlayerName(),
      timeElapse: game.getElapseTime(),
      map: game.getMapName(),
    };
    prevResults.push(goodResult);
    permaSaveJSON("prevResults", prevResults);
  }
}

// ! Handle updates of time elapse ----------------------
function cycle() {
  GamePanel.updateControlPanelInfo(
    game.getPlayerName(),
    game.getMapName(),
    game.getElapseTime()
  );
}

let someInterval = setInterval(cycle, 1000);
