class GamePanel {
  static gameOuterDiv = document.querySelector("#game");
  static gameDiv = document.querySelector("#game_map");
  static gameTable = document.querySelector("#game_map table");
  static controlPanel = document.querySelector("#game_control_panel");

  static updateControlPanelInfo(playerName, mapName, timeElapse) {
    const ps = document.querySelectorAll("#game_control_panel_information p");
    ps[0].innerText = "Player: " + playerName;
    ps[1].innerText = "Map: " + mapName;
    ps[2].innerText = "Time Elapse: " + timeElapse;
  }

  /**
   * Shows the success message.
   * @param {*} hasWon if game has success
   */
  static updateSuccessStatus(hasWon) {
    if (hasWon) {
      document.querySelector("#success-message").innerText =
        "Congratulations, you win!";
    } else {
      document.querySelector("#success-message").innerText = "";
    }
  }

  /**
   * Not used, originally used so that user can only
   * Save, when game is not finished.
   * Restart, when game is finished.
   * @param {*} hasWon if game has success
   */
  static updateControlPanelButton(hasWon) {
    if (hasWon) {
      document.querySelector("#restart").classList.remove("hidden");
      document.querySelector("#save").classList.add("hidden");
      document.querySelector("#success-message").innerText =
        "Congradulations, you win!";
    } else {
      document.querySelector("#restart").classList.add("hidden");
      document.querySelector("#save").classList.remove("hidden");
      document.querySelector("#success-message").innerText = "";
    }
  }

  /**
   * Redraws everything of the map from scratch.
   * @param {*} map the current map object in play.
   */
  static drawMap(map) {
    if (this.gameTable) {
      this.gameDiv.removeChild(this.gameTable);
    }
    this.gameTable = document.createElement("table");

    for (let row = 0; row < map.width; row++) {
      const newTR = document.createElement("tr");
      for (let col = 0; col < map.height; col++) {
        const newTD = document.createElement("td");
        newTD.dataset.rowIndex = row;
        newTD.dataset.colIndex = col;
        newTR.appendChild(newTD);
      }
      this.gameTable.appendChild(newTR);
    }
    this.gameDiv.appendChild(this.gameTable);
    // Draw the black tiles
    map.blackTiles.forEach((tile) => {
      let newBlackTile = this.getTile(tile.y, tile.x);
      newBlackTile.classList.add("BlackTile");
      newBlackTile.innerText = tile.number;
    });
    // Draw placed bulbs
    map.bulbs.forEach((bulb) => {
      let newBulb = this.getTile(bulb.row, bulb.col);
      newBulb.innerText = "💡";
      newBulb.classList.add("BulbTile");
      // Corresponding tiles
      this.drawTilesLightedBy(bulb, map);
    });
    // colour correct black tiles
    map.blackTiles.forEach((tile) => {
      let blackTile = this.getTile(tile.y, tile.x);
      let cnt = this.countBulbsAround(tile);
      if (cnt == tile.number) {
        blackTile.classList.add("FullfilledBlackTile");
      }
    });
  }

  /**
   * Count the neighbouring bulbsof a black tile. (4 direction)
   * @param {*} tile The black tile struct to be check
   * @returns the number of bulbs around
   */
  static countBulbsAround(tile) {
    let cnt = 0;
    for (let i = -1; i <= 1; i++) {
      for (let j = -1; j <= 1; j++) {
        if (Math.abs(i) == Math.abs(j)) {
          continue;
        }
        const toCheck = this.getTile(tile.y + i, tile.x + j);
        // console.log(toCheck);
        if (toCheck) {
          if (toCheck.classList.contains("BulbTile")) {
            cnt++;
          }
        }
      }
    }
    return cnt;
  }
  /**
   * Lights the tiles that are on the same row/col of the given bulb;
   * stops when met a black tile.
   * @param {*} bulb bulb object
   * @param {*} map the user playing map object that sould be lighted
   */
  static drawTilesLightedBy(bulb, map) {
    const bulbRow = parseInt(bulb.row);
    const bulbCol = parseInt(bulb.col);
    // Row
    for (let c = bulbCol - 1; c >= 0; c--) {
      let tileToBeLight = this.getTile(bulbRow, c);
      let meetBlack = this.lightingTile(tileToBeLight, bulbRow, bulbCol);
      if (meetBlack) {
        break;
      }
    }
    for (let c = bulbCol + 1; c < map.width; c++) {
      let tileToBeLight = this.getTile(bulbRow, c);
      let meetBlack = this.lightingTile(tileToBeLight, bulbRow, bulbCol);
      if (meetBlack) {
        break;
      }
    }
    // Column
    // UP
    for (let r = bulbRow - 1; r >= 0; r--) {
      let tileToBeLight = this.getTile(r, bulbCol);
      let meetBlack = this.lightingTile(tileToBeLight, bulbRow, bulbCol);
      if (meetBlack) {
        break;
      }
    }
    // DOWN
    for (let r = bulbRow + 1; r < map.height; r++) {
      let tileToBeLight = this.getTile(r, bulbCol);
      let meetBlack = this.lightingTile(tileToBeLight, bulbRow, bulbCol);
      if (meetBlack) {
        break;
      }
    }
  }
  /**
   * 'Lights up' the given tile.
   * If the tile is a normal white tile, add WhiteTile class to it.
   * If the tile is a black tile, inform caller by return True.
   * If the tile contains a bulb, both tiles become BadTile.
   * @param {*} tileToBeLight the td element that should be lighted.
   * @param {*} bulbRow the row number of the bulb that lights it
   * @param {*} bulbCol the col number of the bulb that lights it.
   * @returns if this td to be lighted is a black tile and will block the light
   */
  static lightingTile(tileToBeLight, bulbRow, bulbCol) {
    if (tileToBeLight.classList.contains("BlackTile")) {
      return true;
    }
    if (tileToBeLight.classList.contains("BulbTile")) {
      let bulbTD = this.getTile(bulbRow, bulbCol);
      bulbTD.classList.add("BadTile");
      tileToBeLight.classList.add("BadTile");
    }
    tileToBeLight.classList.add("LightedTile");
    return false;
  }
  static getTile(row, col) {
    const str =
      'td[data-row-index="' + row + '"][data-col-index="' + col + '"]';
    return this.gameDiv.querySelector(str);
  }

  /**
   * Placing bulb's animation.
   * @param {*} newBulb td element that should place the bulb on
   * @param {*} map map object used by user
   */
  static async beautifulDraw(newBulb, map) {
    // Draw placed bulbs
    newBulb.innerText = "💡";
    newBulb.classList.add("BulbTile");
    await this.beautifulLighting(newBulb, map.width);
    // console.log("finished");
    this.drawMap(map);
  }

  /**
   * Lighting animation the cross of the given bulb one after another,
   * from cloest to bulb to far away.
   * @param {*} bulb a td element that has a bulb on it
   * @param {*} bound the dimension of the board
   */
  static async beautifulLighting(bulb, bound) {
    // console.log("beautifulLighting");
    let upOk = true;
    let downOk = true;
    let rightOk = true;
    let leftOk = true;
    const bulbRow = parseInt(bulb.dataset.rowIndex);
    const bulbCol = parseInt(bulb.dataset.colIndex);

    for (let rad = 1; rad < bound; rad++) {
      upOk = (bulbRow - rad >= 0 ? true : false) && upOk;
      upOk = this.beautifulLightingOne(
        upOk,
        bulbRow - rad,
        bulbCol,
        bulbRow,
        bulbCol
      );
      // down
      downOk = (bulbRow + rad < bound ? true : false) && downOk;
      downOk = this.beautifulLightingOne(
        downOk,
        bulbRow + rad,
        bulbCol,
        bulbRow,
        bulbCol
      );
      // left
      leftOk = (bulbCol - rad >= 0 ? true : false) && leftOk;
      leftOk = this.beautifulLightingOne(
        leftOk,
        bulbRow,
        bulbCol - rad,
        bulbRow,
        bulbCol
      );
      // right
      rightOk = (bulbCol + rad < bound ? true : false) && rightOk;
      rightOk = this.beautifulLightingOne(
        rightOk,
        bulbRow,
        bulbCol + rad,
        bulbRow,
        bulbCol
      );

      if (!upOk && !downOk && !leftOk && !rightOk) {
        break;
      }
      await new Promise((resolve) => {
        setTimeout(() => {
          resolve("good");
        }, 200);
      });
    }
  }

  /**
   * Light up one tile
   * @param {*} ok Is it a good place which can be lighted
   * @param {*} row
   * @param {*} col
   * @param {*} bulbRow The bulb that shines it
   * @param {*} bulbCol
   * @returns if it is good to continue in this direction the lighting
   */
  static beautifulLightingOne(ok, row, col, bulbRow, bulbCol) {
    if (ok) {
      let tileToBeLight = this.getTile(row, col);
      // console.log(row, col);
      let meetBlack = this.beautifulLightingTile(
        tileToBeLight,
        bulbRow,
        bulbCol
      );
      if (meetBlack) {
        ok = false;
      }
    }
    return ok;
  }

  /**
   * Light up one tile.
   * Same as lightingTile, just difference in tile class.
   * @param {*} tileToBeLight td element
   * @param {*} bulbRow
   * @param {*} bulbCol
   * @returns true if met a black tile
   */
  static beautifulLightingTile(tileToBeLight, bulbRow, bulbCol) {
    if (tileToBeLight.classList.contains("BlackTile")) {
      return true;
    }
    if (tileToBeLight.classList.contains("BulbTile")) {
      let bulbTD = this.getTile(bulbRow, bulbCol);
      bulbTD.classList.add("BadTile");
      tileToBeLight.classList.add("BadTile");
    }
    tileToBeLight.classList.add("FlowOfLight");
    return false;
  }
  static getTile(row, col) {
    const str =
      'td[data-row-index="' + row + '"][data-col-index="' + col + '"]';
    return this.gameDiv.querySelector(str);
  }
}

class SelectionPanel {
  static prevRecordsTable = document.querySelector("#prev-records");
  static selectionDiv = document.querySelector("#map-selection");
  static nameForm = document.querySelector("#name-form");
  static resumeBtn = document.querySelector("#resumeBtn");

  static putNewRecordTR(record) {
    const newTR = document.createElement("tr");
    const newTD1 = document.createElement("td");
    newTD1.innerText = record.name;
    newTR.appendChild(newTD1);

    const newTD2 = document.createElement("td");
    newTD2.innerText = record.map;
    newTR.appendChild(newTD2);

    const newTD3 = document.createElement("td");
    newTD3.innerText = record.timeElapse;
    newTR.appendChild(newTD3);

    SelectionPanel.prevRecordsTable.querySelector("tbody").appendChild(newTR);
  }

  /**
   * Deletes the origin tbody of the prev records table,
   * if there exists any.
   * Used for refresh the records within the game (without reload)
   */
  static replaceWithNewTbody() {
    const originalTB = SelectionPanel.prevRecordsTable.querySelector("tbody");
    if (originalTB) {
      SelectionPanel.prevRecordsTable.removeChild(originalTB);
      const newTbody = document.createElement("tbody");
      SelectionPanel.prevRecordsTable.appendChild(newTbody);
    }
  }

  /**
   * @param {*} btnName string of the name of the button
   * @returns JSON object of setting of the chosen map
   */
  static getMapByBtn(btnName) {
    return {
      Easy: {
        map: EASYMAP,
        solution: EASYMAP_SOLUTION,
        mname: "Easy",
        pname: null,
        timeElapse: 0,
      },
      Advanced: {
        map: ADVANCED_MAP,
        solution: ADVANCED_MAP_SOLUTION,
        mname: "Advanced",
        pname: null,
        timeElapse: 0,
      },
      Extreme: {
        map: EXTREME_MAP,
        solution: EXTREME_MAP_SOLUTION,
        mname: "Extreme",
        pname: null,
        timeElapse: 0,
      },
    }[btnName];
  }
}
