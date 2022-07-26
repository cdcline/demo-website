/**
 * It's fun to make the site interative and we can do that with JS.
 *
 * This is a bunch of functions that let us access random elements and change
 * their color at different intervals.
 */
class FunUtils {
   // Number to increment to increase color change frequency
   static funMeter = 0;
   // Number to check against to "hide" secret
   static funLimit = 1;
   // We could stack loops on top but it looks better to stop one interval before you start another
   static funLoop;
   static minSpeed = 1;
   static maxSpeed = 10;
   static maxInterval = 2000;
   static minInterval = 300;
   static setup = false;

   static setupFun(callbackFnc) {
      this.setupOGColors();
      this.setupNoFun();
      // Change some colors
      this.randomColorFun(10)
      // Change colors after we've loaded resources. (lol js)
      JSServerUtils.addOnLoadFunction(e => this.randomColorFun(3));
      // Start building fun with the 'fun-button'
      JSServerUtils.addClickFunctionOnClasses('fun-btn', this.addFun.bind(this));
      if (callbackFnc) {
         callbackFnc();
      }
   }

   // We'd like to use some js array logic so we'll convert from HTML Collection to an array
   static getFunArray() {
      return [...document.getElementsByClassName('fun')];
   }

   static getAllTheThings() {
      // Basically grab all the text elements.
      // NOTE: Div isn't included b/c it makes going back to og colors difficult
      return [...document.querySelectorAll('p, span, li, h1, h2, h3, h4, h5, h6, a, td, th, caption, code')];
   }

   // Go through each "fun" element and set a random color on it
   static randomColorFun(colorIntervalMS) {
      let funArray = this.getFunArray();
      // If we don't shuffle, the order is the same. The rainbow effect is ok but better if randomized.
      funArray = MathUtils.shuffleArray(funArray);
      // If we do a delay, we can really randomize when colors change
      let maxColorChangeInterval = colorIntervalMS ? colorIntervalMS : this.maxInterval;
      function delay(time) {
         return new Promise(resolve => setTimeout(resolve, time));
      };

      // Go through all our fun elements
      funArray.forEach(function(el) {
         // Grab a random delay time
         let randomTime = MathUtils.getRandomIntInclusive(1, maxColorChangeInterval);
         delay(randomTime).then(() => {
            // We might have removed fun since the delay so make sure we still
            // want to change colors
            if (el.classList.contains('fun')) {
               el.style.color = MathUtils.getRandomRGB()
            }
         });
      });
   }

   static getMaxIntervalFromSpeed(speed) {
      if (speed < this.minSpeed) {
         speed = this.minSpeed;
      } else if (speed > this.maxSpeed) {
         speed = this.maxSpeed;
      }
      // Should go as speed increases, the "Max interval between things" reduces
      let fractionSpeed = (speed/this.maxSpeed);
      let speedInterval = this.maxInterval - (this.maxInterval * fractionSpeed);
      return speedInterval < this.minInterval ? this.minInterval : speedInterval;
   }

   // Makes all the text elements on the page change to different colors
   // Gives a rainbow shimmery look
   // The higher the speed, the faster all the elements will shift colors again
   static funBoom(speedIn) {
      // Select all the things that contain text
      this.getAllTheThings().forEach(function(el) {
         if (!el.classList.contains('fun')) {
            el.classList.add('fun');
            FunUtils.setupNoFunEvents(el);
         }
      });

      // Clear any existing interval. Looks better when there's one loop at a consistant interval
      if (this.funLoop) {
         clearInterval(this.funLoop);
      }
      let maxIntervalMS = this.getMaxIntervalFromSpeed(speedIn);
      let randomlyChangeAllColors = function(maxIntervalMS) {
         this.randomColorFun(maxIntervalMS);
      }.bind(this);
      // Change all the colors now
      randomlyChangeAllColors();
      // Setup the loop to change colors in maxIntervalMS
      this.funLoop = setInterval(randomlyChangeAllColors, maxIntervalMS);
   }

   static addFun() {
      // If we reached our limit, start the fun boom
      if (++this.funMeter >= this.funLimit) {
         this.funBoom(this.funMeter);
      // Otherwise we randomize the existing colors and hope they click a few more times
      } else {
         this.randomColorFun();
      }
   }

   static setupNoFunEvents(el) {
      // If we've already setup the no-fun-events, bail
      if (el.getAttribute('data-no-fun-event') === 'set') {
         return;
      }
      // Anytime the element gets a mouseover
      el.addEventListener('mouseenter', e => {
         // Stop any color changing
         el.classList.remove('fun');
         // Store the current color
         el.setAttribute('data-fun-color', el.style.color);
         // Set it to the original color
         el.style.color = el.getAttribute('data-og-color');
      });
      // When the mouse leaves
      el.addEventListener('mouseleave', e => {
         // Set back the original color
         el.style.color = el.getAttribute('data-fun-color');
         let addFun = function(el) {
            el.classList.add('fun');
         };
         // Add back in color changing
         setInterval(addFun(el), 500);
      });
      // Mark that we setup the events
      el.setAttribute('data-no-fun-event', 'set');
   }

   static setupNoFun() {
      this.getFunArray().forEach(this.setupNoFunEvents);
   }

   // It gets weird with timings if we try to do this while also changing all
   // the colors in the interval so we'll just do it early.
   //
   // It's kinda a waste but also there really aren't that many properties to
   // worry about.
   static setupOGColors() {
      this.getFunArray().forEach(el => {
         el.setAttribute('data-og-color', window.getComputedStyle(el).getPropertyValue('color'));
      });
   }

}