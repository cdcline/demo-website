class WelcomeHeader {
   static circleLoopInterval;
   static loopCircles;

   static init() {
      this.loopCircles = [...document.getElementsByClassName('js-floating-circle')].forEach(function(el) {
         let fCircle = new FloatingCircle(el);
         fCircle.loop();
         return fCircle;
      });

   }
}

class FloatingCircle {
	constructor(circleEl) {
      this.circleEl = circleEl;
      this.intervalId = null;
      this.resetValues();
   }

   loop() {
      clearTimeout(this.intervalId);
      this.resetValues();
      this.move();
      this.intervalId = setTimeout(this.loop.bind(this), this.loopTime * 1000);
   }

   move() {
      let sTransform ='translate(' + this.toX + '%,' + this.toY+ '%)';
      this.circleEl.style.transform = sTransform;
      this.circleEl.style.transitionDuration = this.loopTime + 's';
   }

   resetValues() {
      this.loopTime = MathUtils.getRandomIntInclusive(5, 15);
      this.toX = MathUtils.getRandomIntInclusive(-20, 20);
      this.toY = MathUtils.getRandomIntInclusive(-20, 20);
   }
}