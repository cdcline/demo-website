class RobotsHeader {
   static robots = null;
   static wild = 0;
   static wildScale = 5;
   static standardSpeed = 120;
   static maxSpeed = 10;

   static init() {
      this.robots = [...document.getElementsByClassName('js-header-robot')].map(function(el) {
         var robot= new Robot(el);
         robot.rollOut();
         return robot;
      });
   }

   static goWild() {
      this.wild++;
      let speed = Math.max(this.maxSpeed, this.standardSpeed - (this.wild * this.wildScale));
      [...document.getElementsByClassName('robots-header-moon')].map(function(el) {
         el.style.animationDuration = speed + 's';
      });
   }
}

class Robot {
   searchSpace = 20
   maxSpeed = 3000
   minSpeed = 100
   speedFuzz = 50
   wildScale = 250
   minAngle = -15
   maxAngle = 15
   minBump = -5
   maxBump = 5

	constructor(robotEl) {
      this.robotEl = robotEl;
      this.wild = 0;
      this.robotEl.addEventListener('click', function(ev) {this.goWild()}.bind(this));
      this.goingRight = true;
      this.moved = 0;
      this.angle = 0;
      this.loopTimeoutId = null;
   }

   rollOut() {
      this.clearLoopTimeout();
      this.move();
      this.loopTimeoutId = setTimeout(this.rollOut.bind(this), this.getNewSpeed());
   }

   goWild() {
      this.wild++;
      this.rollOut()
      RobotsHeader.goWild();
   }

   move() {
      this.chartDirection();
      let dir = this.goingRight ? 1 : -1;
      let direction = 'scaleX(' + dir + ')';
      this.chartAngle();
      let rotation = 'rotate('+ this.angle + 'deg)';
      this.chartBump();
      let translate = 'translate(' + this.toX + '%,' + this.toY+ '%)';
      let transform = rotation + ' ' + direction + ' ' + translate;
      this.robotEl.style.transform = transform;
   }

   chartDirection() {
      this.moved++;
      let maxChance = Math.max(this.searchSpace - this.moved, 1);
      let randomChance = MathUtils.getRandomIntInclusive(0, maxChance)
      if (randomChance === 0) {
         this.moved = 0;
         this.goingRight = this.goingRight ? false : true;
      }
   }

   chartAngle() {
      this.angle = MathUtils.getRandomIntInclusive(this.minAngle, this.maxAngle);
   }

   chartBump() {
      this.toX = MathUtils.getRandomIntInclusive(this.minBump, this.maxBump);
      this.toY = MathUtils.getRandomIntInclusive(this.minBump, this.maxBump);
   }

   getNewSpeed() {
      let speed = this.maxSpeed;

      if (this.wild) {
         speed = this.maxSpeed / 2;
         speed = Math.max(this.minSpeed, speed - (this.wild * this.wildScale));
      }
      let minSpeed = Math.max(50, speed - this.speedFuzz);
      let maxSpeed = Math.max(50, speed + this.speedFuzz);
      return MathUtils.getRandomIntInclusive(minSpeed, maxSpeed);
   }

   clearLoopTimeout() {
      if (typeof this.loopTimeoutId === 'number') {
         clearTimeout(this.loopTimeoutId);
      }
   }
}
