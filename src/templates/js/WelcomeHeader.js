class WelcomeHeader {
   static circleLoopInterval;
   static loopCircles;
   static resetting = false;

   static init() {
      this.loopCircles = [...document.getElementsByClassName('js-floating-circle')].map(function(el) {
         var fCircle = new FloatingCircle(el);
         fCircle.loop();
         return fCircle;
      });

      this.slashLoops = [...document.getElementsByClassName('js-welcome-header-slash-container')].map(function(el) {
         let sLoop = new SlashLoop(el);
         sLoop.animate();
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

      let allowResetting = function() {
         this.resetting = false;
      }.bind(this);
      [...document.getElementsByClassName('welcome-header-contact-info-image')].forEach(function(el) {

         el.addEventListener('click', function(ev) {
            this.loopCircles.forEach(el => {
               el.randomImage();
            });

            if (!this.resetting) {
               this.resetting = true;
            } else {
               return;
            }
            setTimeout(allowResetting, 1000);

            this.hiddenTextContainers.forEach(el => {
               el.fullReset();
            });
         }.bind(this));
      }.bind(this));
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

   randomImage() {
      let curIndex = parseInt(this.circleEl.getAttribute('data-src-index'));
      if (isNaN(curIndex) || curIndex < 1) {
         return;
      }

      let newImageIndex = curIndex;
      let max = 10;
      let i = 0;
      while (newImageIndex === curIndex) {
         if (i++ > max) {
            break;
         }
         newImageIndex = MathUtils.getRandomIntInclusive(1, 3);
      }

      let newSrcAttribute = 'data-src-' + newImageIndex;
      let newSrc = this.circleEl.getAttribute(newSrcAttribute);
      this.circleEl.setAttribute('data-src-index', newImageIndex);
      this.circleEl.src = newSrc;
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
      this.animating = false;
      this.slashContainer.addEventListener('transitionend', function(el) {
         if (this.animating) {
            this.resetAnimation();
         } else {
            this.animate();
         }
      }.bind(this));
      this.slashEl = this.slashContainer.querySelector('.js-moving-slash');
      this.timeoutId = null;
   }

   animate() {
      this.animating = true;
      let loopTime = MathUtils.getRandomIntInclusive(5, 15);
      this.slashEl.style.transitionDuration = loopTime + 's';
      this.slashEl.classList.add('js-animate-slash');
   }

   resetAnimation() {
      this.animating = false;
      let waitTime = MathUtils.getRandomIntInclusive(1, 5);
      this.slashEl.style.transitionDuration = waitTime + 's';
      this.slashEl.classList.remove('js-animate-slash');
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

   fullReset() {
      this.clearLoopTimeout();
      this.clearDelayTimeout();
      this.hideText();
      this.loop();
   }

   loop() {
      this.setRandomTime();
      this.clearLoopTimeout();
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
      this.clearDelayTimeout();

      this.delayTimoutId = setTimeout(function() {
         // Confusing but we want the text to "flash" so we hide it before we start the animation
         this.hideText();
         this.textConcealer.classList.add('js-header-animation-running');
         this.textConcealer.classList.add(this.getStartAnimationClass());
      }.bind(this), this.getDelay());
   }

   closeAnimation() {
      this.revealing = false;
      this.closing = true;
      this.resetting = false;
      // Now that we've animated a box across the whole div, we reveal the hidden text
      this.showText();
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

   showText() {
      this.text.classList.add('js-show-hidden-text');
   }

   hideText() {
      this.text.classList.remove('js-show-hidden-text');
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

   clearDelayTimeout() {
      if (this.delayTimoutId) {
         clearTimeout(this.delayTimoutId);
      }
   }

   clearLoopTimeout() {
      if (this.loopTimeoutId) {
         clearTimeout(this.loopTimeoutId);
      }
   }
}