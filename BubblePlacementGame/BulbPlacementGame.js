class BulbPlacementGame {
  constructor(map, solution, timeElapse, mapName, playerName) {
    this.map = map;
    this.solution = solution;
    this.startTime = new Date();
    this.timeElapse = 0;
    this.defaultElased = timeElapse;
    this.mapName = mapName;
    this.playerName = playerName;
  }
  
  setPlayerName(newName) {
    this.playerName = newName;
  }

  /**
   * Reset everything tof game to initial
   */
  reset() {
    this.map.bulbs = [];
    this.startTime = new Date();
    this.timeElapse = 0;
    this.defaultElased = 0;
  }

  /**
   * push into map.bulbs
   * @param {*} newBulb bulb object
   */
  addBulb(newBulb) {
    this.map.bulbs.push(newBulb);
  }

  /**
   * remove if exists
   * @param {*} bulb bulb object
   */
  removeBulb(bulb) {
    this.map.bulbs = this.map.bulbs.filter(
      (elem) => elem.row != bulb.row || elem.col != bulb.col
    );
  }
  /**
   * 
   * @returns Time elapse from the initialization of the game.
   */
  getElapseTime() {
    if (!this.checkSolution()) {
      let endTime = new Date();
      var diff = endTime - this.startTime;
      diff /= 1000;
      this.timeElapse = Math.round(diff);
    }
    return this.timeElapse + this.defaultElased;
  }
  getMap() {
    return this.map;
  }
  getPlayerName() {
    return this.playerName;
  }
  getMapName() {
    return this.mapName;
  }
  getBulbs() {
    return this.bulbs;
  }

  /**
   * @returns true if solutions match
   */
  checkSolution() {
    if (this.map.bulbs.length != this.solution.length) {
      return false;
    }
    // Every user bulb appears in the solution
    for (const userBulb of this.map.bulbs) {
      if (
        this.solution.filter(
          (solBulb) => solBulb.x == userBulb.col && solBulb.y == userBulb.row
        ).length <= 0
      ) {
        return false;
      }
    }
    return true;
  }

  /**
   * @returns settings of the current in a single JSON object
   */
  toJson() {
    return {
      map: this.map,
      solution: this.solution,
      timeElapse: this.timeElapse + this.defaultElased,
      mapName: this.mapName,
      playerName: this.playerName,
    };
  }
}
