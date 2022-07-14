class WelcomeHeader {
   static circleLoopInterval;
   static loopCircles;

   static init() {
      this.loopCircles = [...document.getElementsByClassName('js-floating-circle')].forEach(function(el) {
         let fCircle = new FloatingCircle(el);
         fCircle.loop();
         return fCircle;
      });

      this.slashLoop = new SlashLoop(document.getElementById('welcome-header-slash-container'), document.getElementById('welcome-header-slash'));
      this.slashLoop.loop();
   }
}

class FloatingCircle {
	constructor(circleEl) {
      this.circleEl = circleEl;
      this.timeoutId = null;
      this.resetValues();
   }

   loop() {
      clearTimeout(this.timeoutId);
      this.resetValues();
      this.move();
      this.timeoutId = setTimeout(this.loop.bind(this), this.loopTime * 1000);
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

class SlashLoop {
   constructor(slashContainer, slashEl) {
      this.slashContainer = slashContainer;
      this.slashContainer.addEventListener('transitionend', function(el) {
         this.resetAnimation();
      }.bind(this));
      this.slashEl = slashEl;
      this.timeoutId = null;
   }

   loop() {
      this.setRandomTime();
      if (this.timeoutId) {
         clearTimeout(this.timeoutId);
         this.resetAnimation();
      }
      this.move();
      this.timeoutId = setTimeout(this.loop.bind(this), this.loopTime * 1000);
   }

   move() {
      let sTransform = 'translateX(350%)';
      this.slashEl.style.transform = sTransform;
      this.slashEl.style.transitionDuration = this.loopTime + 's';
   }

   setRandomTime() {
      this.loopTime = MathUtils.getRandomIntInclusive(5, 15);
   }

   resetAnimation() {
      let sTransform = 'translateX(0)';
      this.slashEl.style.transform = sTransform;
      this.slashEl.style.transitionDuration = '0s';
   }
}