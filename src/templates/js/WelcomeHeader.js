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

            this.birdCage.goWild();

            if (!this.resetting) {
               this.resetting = true;
            } else {
               return;
            }
            setTimeout(allowResetting, 1000);

            this.hiddenTextContainers.forEach(el => {
               el.fullReset();
            });

            this.floatXBoxes.forEach(el => {
               el.goWild();
            });

            this.slashLoops.forEach(el => {
               el.goWild();
            });

            this.loopCircles.forEach(el => {
               el.goWild();
            });
         }.bind(this));
      }.bind(this));

      this.birdCage = new FlyingBirdCage(document.getElementById('welcome-header-flying-bird-cage'));
      this.birdCage.loop();
   }
}

class FloatingCircle {
	constructor(circleEl) {
      this.circleEl = circleEl;
      this.timeoutId = null;
      this.wild = false;
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
      if (this.wild) {
         this.changeColor();
      }
      this.circleEl.style.transform = sTransform;
      this.circleEl.style.transitionDuration = this.loopTime + 's';
   }

   changeColor() {
      if (this.circleEl.classList.contains('top-floating-circle')) {
         return;
      }
      this.circleEl.style.backgroundColor = MathUtils.getRandomRGB();
   }

   resetValues() {
      let minLoopTime = 5;
      let maxLoopTime = 15;
      let minXMove = -20;
      let maxXMove = 20;
      let minYMove = -20;
      let maxYMove = 20;
      if (this.wild) {
         minLoopTime = 1;
         maxLoopTime = 30;
         minXMove = minYMove = -50;
         maxXMove = maxYMove = 50;
      }

      this.loopTime = MathUtils.getRandomIntInclusive(minLoopTime, maxLoopTime);
      this.toX = MathUtils.getRandomIntInclusive(minXMove, maxXMove);
      this.toY = MathUtils.getRandomIntInclusive(minYMove, maxYMove);
   }

   goWild() {
      this.wild = true;
      this.changeColor();
   }
}

class SlashLoop {
   constructor(slashContainer) {
      this.slashContainer = slashContainer;
      this.animating = false;
      this.loopTime = 1;
      this.slashContainer.addEventListener('transitionend', function(el) {
         if (this.animating) {
            this.resetAnimation();
         } else {
            this.animate();
         }
      }.bind(this));
      this.slashEl = this.slashContainer.querySelector('.js-moving-slash');
      this.slashParts = [...this.slashContainer.querySelectorAll('.js-poly-slash-piece')];
      this.timeoutId = null;
   }

   animate() {
      this.animating = true;
      this.loopTime = MathUtils.getRandomIntInclusive(5, 15);
      if (this.wild) {
         this.changeColors();
      }
      this.slashEl.style.transitionDuration = this.loopTime + 's';
      this.slashEl.classList.add('js-animate-slash');
   }

   resetAnimation() {
      this.animating = false;
      let waitTime = MathUtils.getRandomIntInclusive(1, 5);
      this.slashEl.style.transitionDuration = waitTime + 's';
      this.slashEl.classList.remove('js-animate-slash');
   }

   goWild() {
      this.wild = true;
      this.changeColors();
   }

   changeColors() {
      this.slashParts.forEach(function(el) {
         el.style.backgroundColor = MathUtils.getRandomRGB();
         el.style.transitionDuration = this.loopTime + 's';
      }.bind(this));
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

   goWild() {
      this.xBoxes.forEach(function(xBox) {xBox.goWild()});
   }
}

class FloatingXBox {
   constructor(xBoxEl) {
      this.xBox = xBoxEl;
      this.resetTimeoutid = null;
      this.originalColor = this.xBox.style.backgroundColor;
      this.wild = false;
      this.tempColor = null;
      this.wildTimeoutid = null;

      this.xBox.addEventListener('click', function() {
         this.changeColor();
         this.originalColor = this.xBox.style.backgroundColor;
         this.goWild();
      }.bind(this));
   }

   setupFloat(maxFloatTime) {
      // Randomly don't have one move
      let shouldMove = MathUtils.getRandomIntInclusive(0, 2);
      if (!shouldMove) {
         return;
      }
      if (this.resetTimeoutid) {
         clearTimeout(this.resetTimeoutid);
      }
      this.maxMoveTime = parseInt(maxFloatTime / 2);
      this.floatToRandomSpot();
      this.resetTimeoutid = setTimeout(this.floatHome.bind(this), this.maxMoveTime * 1000);
   }

   floatToRandomSpot() {
      let toX = MathUtils.getRandomIntInclusive(-50, 50);
      let toY = MathUtils.getRandomIntInclusive(-50, 50);
      if (this.wild) {
         toX = toX * MathUtils.getRandomIntInclusive(1, 10);
         toY = toY * MathUtils.getRandomIntInclusive(1, 10);
      }
      let sTransform ='translate(' + toX + '%,' + toY+ '%)';
      this.xBox.style.transform = sTransform;
      // Kinda arbitrary but this will change x move speeds
      // If > "maxMoveTime" the X won't actually go the full set distance
      // This duration gives a pretty good, random, slow "floaty" feel
      let multiplier = this.wild ? MathUtils.getRandomIntInclusive(2, 10) : 2;
      let duration = this.maxMoveTime * multiplier;
      this.xBox.style.transitionDuration = duration + 's';
   }

   floatHome() {
      let sTransform ='translate(0%, 0%)';
      this.xBox.style.transform = sTransform;
   }

   goWild() {
      if (this.wildTimeoutid) {
         clearTimeout(this.wildTimeoutid);
      }

      let randomColorTime = MathUtils.getRandomIntInclusive(5, 20);
      this.wildTimeoutid = setTimeout(this.changeColor.bind(this), randomColorTime * 1000);
      if (!this.wild) {
         this.wild = true;
         this.xBox.addEventListener('mouseenter', function() {
            this.tempColor = this.xBox.style.backgroundColor;
            this.xBox.style.backgroundColor = this.originalColor;
         }.bind(this));
         this.xBox.addEventListener('mouseleave', function() {
            if (this.tempColor) {
               this.xBox.style.backgroundColor = this.tempColor;
               this.tempColor = null;
            }
         }.bind(this));
      }
   }

   changeColor() {
      this.xBox.style.backgroundColor = MathUtils.getRandomRGB();
   }
}

class HiddenTextContainer {
   constructor(hidddenTextContainer) {
      this.textContainer = hidddenTextContainer;
      this.textConcealer = this.textContainer.querySelector('.hidden-text-concealer');
      this.text = this.textContainer.querySelector('.hidden-text');
      this.aMode = null;
      this.loopTimeoutid = null;
      this.delayTimoutId = null;
      this.textContainer.addEventListener('transitionend', function(ev) {
         let isSlideAnimation = ev.propertyName === 'transform';
         if (isSlideAnimation) {
            switch (this.aMode) {
               case 1:
                  this.startAnimation();
                  break;
               case 2:
                  this.closeAnimation();
                  break;
            }
         }
      }.bind(this));
   }

   fullReset() {
      this.clearLoopTimeout();
      // This adds an odd behaviour if you click while the "animation" is running
      // but it eventually gets back into a "good" state. I think you could fix
      // with some special "mode" logic but I ended up liking the behavior, it's
      // kinda rare to get, gives a different behavior and eventually works so
      // feels appropriate.
      //
      // Funny because it's difficult to catch, you have to click after the
      // animation timer starts and before the end animation begins. With the
      // random 5-10 second timer it's difficult to replicate.
      //
      // NOTE: To replicate: change the loop time to ~1 second.
      this.clearDelayTimeout();
      this.hideText();
      this.loop();
   }

   loop() {
      this.setRandomTime();
      this.reset();
      this.loopTimeoutId = setTimeout(this.loop.bind(this), this.loopTime * 1000);
   }

   reset() {
      this.aMode = 1;
      if (this.delayTimoutId) {
         this.textConcealer.classList.remove('js-header-animation-running');
         this.textConcealer.classList.remove(this.getStartAnimationClass());
         this.textConcealer.classList.remove(this.getEndAnimationClass());
      } else {
         this.startAnimation();
      }
   }

   startAnimation() {
      this.aMode = 2;
      this.clearDelayTimeout();

      this.delayTimoutId = setTimeout(function() {
         // Confusing but we want the text to "flash" so we hide it before we start the animation
         this.hideText();
         this.textConcealer.classList.add('js-header-animation-running');
         this.textConcealer.classList.add(this.getStartAnimationClass());
         this.changeColor();
      }.bind(this), this.getDelay());
   }

   closeAnimation() {
      this.aMode = 3;
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

   // We want the text to load in a pattern b/c it looks cool.
   // We get that behavior delaying the load for each element.
   //
   // The "refresh" will also get a delay but that's fine, it
   // adds to the "randomness" of the loop
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
      if (typeof this.timeoutID === 'number') {
         clearTimeout(this.delayTimoutId);
      }
   }

   clearLoopTimeout() {
      if (typeof this.loopTimeoutId === 'number') {
         clearTimeout(this.loopTimeoutId);
      }
   }

   changeColor() {
      // This will start white but if the "fun" logic is running it will
      // match the color of the text being revealed
      this.textConcealer.style.backgroundColor = this.text.style.color;
   }
}

class FlyingBirdCage {
   constructor(birdCage) {
      this.birdCage = birdCage;
      this.babyBird = [...this.birdCage.getElementsByClassName('js-flying-bird')][0];
      this.theWild = document.getElementById('background-container');
      this.birds = [];
      this.freeNewBird();
   }

   loop() {
      clearTimeout(this.timeoutId);
      this.releaseBirds();
      let moveTime = MathUtils.getRandomIntInclusive(8, 13);
      this.timeoutId = setTimeout(this.loop.bind(this), moveTime * 1000);
   }

   releaseBirds() {
      this.birds.forEach(function(bird) {
         bird.move();
      });
   }

   freeNewBird() {
      let newBird = this.babyBird.cloneNode();
      this.theWild.prepend(newBird);
      let bFlyingBird = new FlyingBird(newBird);
      bFlyingBird.move();
      this.birds.push(bFlyingBird);
      return newBird;
   }

   goWild() {
      this.freeNewBird();
      let numBirds = this.birds.length;
      this.birds.forEach(function(bird) {
         bird.goWild(numBirds);
      });
   }
}

class FlyingBird {
   constructor(birdEl) {
      this.bird = birdEl;
      this.toX = null;
      this.toY = null;
      this.toScale = null;
      this.turn = null;
      this.wild = false;
      this.moveTime = null;
   }

   move() {
      this.setNewLocation();
      let sTransform ='translate(' + this.toX + '%,' + this.toY+ '%) scale(' + this.toScale + ') rotate(' + this.turn + 'turn)';
      this.bird.style.transform = sTransform;
      this.bird.style.transitionDuration = this.flyTime + 's';
   }

   // This is all a bit silly but I wanted the birds to fly to the "edge" of
   // the screen b/c it's hidden by the page content otherwise.
   //
   // Also thought it would be fun to scale them and rotate them when they go
   // "wild"
   setNewLocation() {
      this.toScale = this.randomScale();
      this.flyTime = this.randomTime();
      this.turn = this.randomTurn();

      // Choose to move to "sides" or "top/bottom"
      if (this.moveToSide()) {
         // We're moveing to a side so choose left or right
         this.toX = this.moveToLeftSide() ? this.randomLeftX() : this.randomRightX();
         // Move to any Y on that side (it should be visible through the padding)
         this.toY = this.randomAnyY();
      } else {
         // We're moving to the top so pick top or bottom
         // NOTE: It's weighed more for the bottom b/c the top doesn't have any padding
         if (this.moveToTop()) {
            this.toY = this.randomTopY();
            // There's no padding in the top center so move either to the left or right if we're moving to the top
            this.toX = this.moveToLeftSide() ? this.randomLeftX() : this.randomRightX();
         } else {
            this.toY = this.randomBottomY();
            this.toX = this.anyRandomX();
         }
      }
   }

   goWild(numBirds) {
      this.wild = numBirds;
   }

   moveToSide() {
      return this.randomBool();
   }

   moveToLeftSide() {
      return this.randomBool();
   }

   moveToTop() {
      return MathUtils.getRandomIntInclusive(0, 4) < 1;
   }

   randomBool() {
      return MathUtils.getRandomIntInclusive(0, 1) > 0;
   }

   randomScale() {
      // I wanted the birds to get smaller as more got added but not too small
      let wildScale = this.wild ? this.wild : 1;
      let cappedWildScale = wildScale > 5 ? 5 : wildScale;
      let maxNewScale = 80;
      let minNewScale = 50;
      let minScale = 30;
      // Kinda an arbitrary min / max function with a cap to keep from too small
      if (wildScale > 1) {
         maxNewScale -= ((cappedWildScale - 1) * 10);
         minNewScale -= ((cappedWildScale - 1) * 10);
         minNewScale = minNewScale < minScale ? minScale : minNewScale;
      }
      return MathUtils.getRandomIntInclusive(minNewScale, maxNewScale) / 100;
   }

   randomTime() {
      return MathUtils.getRandomIntInclusive(3, 10);
   }

   randomTurn() {
      return this.wild ? MathUtils.getRandomIntInclusive(0, 100) / 100 : 0;
   }

   randomLeftX() {
      return MathUtils.getRandomIntInclusive(-50, -60)
   }

   randomRightX() {
      let viewWidth = this.width();
      // Aritrary but grows with window size.
      let rightX = viewWidth / 20;

      return MathUtils.getRandomIntInclusive(rightX - 10, rightX + 10)
   }

   width() {
      return window.innerWidth
          || document.documentElement.clientWidth
          || document.body.clientWidth
          || 0;
   }

   anyRandomX() {
      return MathUtils.getRandomIntInclusive(this.randomLeftX(), this.randomRightX())
   }

   randomTopY() {
      return MathUtils.getRandomIntInclusive(-25, 50)
   }

   randomBottomY() {
      // Bottom of either the page or header
      return this.randomBool() ?
       MathUtils.getRandomIntInclusive(15, 30) :
       this.randomBottomHeight();
   }

   randomBottomHeight() {
      // Super arbitrary. The ratio seemed to work as the page scaled up.
      let height = this.height() / 8;
      return MathUtils.getRandomIntInclusive(height - 10, height + 10);
   }

   randomAnyY() {
      return MathUtils.getRandomIntInclusive(this.randomTopY(), this.randomBottomHeight());
   }

   height() {
      return document.getElementById('background-container').clientHeight;
   }
}