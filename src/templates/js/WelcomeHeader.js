class WelcomeHeader {
   static circleLoopInterval;
   static loopCircles;

   static init() {
      this.loopCircles = [...document.getElementsByClassName('js-floating-circle')].map(function(el) {
         var fCircle = new FloatingCircle(el);
         fCircle.loop();
         return fCircle;
      });

      this.slashLoops = [...document.getElementsByClassName('js-welcome-header-slash-container')].map(function(el) {
         let sLoop = new SlashLoop(el);
         sLoop.loop();
         return sLoop;
      }.bind(this));

      this.floatXBoxes = [...document.getElementsByClassName('js-welcome-header-x-container')].map(function(el) {
         let xBox = new FloatingXBoxContainer(el);
         xBox.loop();
         return xBox;

      });

      this.hiddenTextContainers = [...document.getElementsByClassName('js-hidden-text-container')].map(function(el) {
         let htContainer = new HiddenTextContainer(el);
         htContainer.loop();
         return htContainer;
      });
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
   constructor(slashContainer) {
      this.slashContainer = slashContainer;
      this.slashContainer.addEventListener('transitionend', function(el) {
         this.resetAnimation();
      }.bind(this));
      this.slashEl = this.slashContainer.querySelector('.js-moving-slash');
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
      let sTransform = 'translateX(285%)';
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

class FloatingXBoxContainer {
   constructor(xBoxContainer) {
      this.xBoxContainer = xBoxContainer;
      this.xBoxes = [...this.xBoxContainer.querySelectorAll('.js-floating-x')].map(function(el) {
         let fXBox = new FloatingXBox(el);
         return fXBox;
      });
      this.timeoutId = null;
   }

   loop() {
      this.setRandomTime();
      if (this.timeoutId) {
         clearTimeout(this.timeoutId);
      }
      this.move();
      this.timeoutId = setTimeout(this.loop.bind(this), this.loopTime * 1000);
   }

   move() {
      this.xBoxes.forEach(function(fXBox) {
         fXBox.setupFloat(this.loopTime);
      }.bind(this));
   }

   setRandomTime() {
      this.loopTime = MathUtils.getRandomIntInclusive(5, 15);
   }
}

class FloatingXBox {
   constructor(xBoxEl) {
      this.xBox = xBoxEl;
      this.resetTimeoutid = null;
   }

   setupFloat(maxFloatTime) {
      // Randomly don't have one move
      let shouldMove = MathUtils.getRandomIntInclusive(0, 2);
      if (!shouldMove) {
         return;
      }
      this.maxMoveTime = parseInt(maxFloatTime / 2);
      this.floatToRandomSpot();
      this.resetTimeoutid = setTimeout(this.floatHome.bind(this), this.maxMoveTime * 1000);
   }

   floatToRandomSpot() {
      let toX = MathUtils.getRandomIntInclusive(-50, 50);
      let toY = MathUtils.getRandomIntInclusive(-50, 50);
      let sTransform ='translate(' + toX + '%,' + toY+ '%)';
      this.xBox.style.transform = sTransform;
      // Kinda arbitrary but this will change x move speeds
      // If > "maxMoveTime" the X won't actually go the full set distance
      // This duration gives a pretty good, random, slow "floaty" feel
      let duration = this.maxMoveTime * 2 + 's';
      this.xBox.style.transitionDuration = duration;
   }

   floatHome() {
      let sTransform ='translate(0%, 0%)';
      this.xBox.style.transform = sTransform;
   }
}

class HiddenTextContainer {
   constructor(hidddenTextContainer) {
      this.textContainer = hidddenTextContainer;
      this.textConcealer = this.textContainer.querySelector('.hidden-text-concealer');
      this.text = this.textContainer.querySelector('.hidden-text');
      this.resetting = false;
      this.revealing = false;
      this.closing = false;
      this.loopTimeoutid = null;
      this.delayTimoutId = null;
      this.textContainer.addEventListener('transitionend', function(el) {
         if (this.resetting) {
            this.show();
         } else if (this.revealing) {
            this.closeAnimation();
         }
      }.bind(this));
   }

   loop() {
      this.setRandomTime();
      if (this.loopTimeoutId) {
         clearTimeout(this.loopTimeoutId);
      }
      this.reset();
      this.loopTimeoutId = setTimeout(this.loop.bind(this), this.loopTime * 1000);
   }

   reset() {
      this.revealing = false;
      this.closing = false;
      this.resetting = true;
      if (this.delayTimoutId) {
         this.textConcealer.classList.remove('js-header-animation-running');
         this.textConcealer.classList.remove(this.getStartAnimationClass());
         this.textConcealer.classList.remove(this.getEndAnimationClass());
      } else {
         this.show();
      }
   }

   show() {
      this.revealing = true;
      this.closing = false;
      this.resetting = false;
      if (this.delayTimoutId) {
         clearTimeout(this.delayTimoutId);
      }

      this.delayTimoutId = setTimeout(function() {
         this.text.classList.remove('js-show-hidden-text');
         this.textConcealer.classList.add('js-header-animation-running');
         this.textConcealer.classList.add(this.getStartAnimationClass());
      }.bind(this), this.getDelay());
   }

   closeAnimation() {
      this.revealing = false;
      this.closing = true;
      this.resetting = false;
      this.text.classList.add('js-show-hidden-text');
      this.textConcealer.classList.add(this.getEndAnimationClass());
   }

   getStartAnimationClass() {
      return this.isHeaderArea() ?
        'js-header-animation-start' :
        'js-grid-animation-start';
   }

   getEndAnimationClass() {
      return this.isHeaderArea() ?
        'js-header-animation-end' :
        'js-grid-animation-end';
   }

   isHeaderArea() {
      if (this.textContainer.classList.contains('js-delay-one') || this.textContainer.classList.contains('js-delay-two')) {
         return true;
      }
      return false;
   }

   getDelay() {
      if (this.textContainer.classList.contains('js-delay-one')) {
         return 0;
      }
      if (this.textContainer.classList.contains('js-delay-two')) {
         return 200;
      }
      if (this.textContainer.classList.contains('js-delay-three')) {
         return 400;
      }
      if (this.textContainer.classList.contains('js-delay-four') || this.textContainer.classList.contains('js-delay-five')) {
         return 600;
      }
      if (this.textContainer.classList.contains('js-delay-six')) {
         return 800;
      }
   }

   setRandomTime() {
      this.loopTime = MathUtils.getRandomIntInclusive(5, 10);
   }
}