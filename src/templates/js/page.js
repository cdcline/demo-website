class MathUtils {
   // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/random#getting_a_random_integer_between_two_values_inclusive
   static getRandomIntInclusive(min, max) {
      let minInt = Math.ceil(min);
      let maxInt = Math.floor(max);
      return Math.floor(Math.random() * (maxInt - minInt + 1) + minInt); //The maximum is inclusive and the minimum is inclusive
   }

   static getRandomColor() {
      return this.getRandomIntInclusive(0, 255);
   }

   static getRandomRGB() {
      let r = this.getRandomColor();
      let g = this.getRandomColor();
      let b = this.getRandomColor();
      return "rgb(" + r + "," + g + "," + b + ")";
   }

   // SO to the rescue: https://stackoverflow.com/a/2450976
   static shuffleArray(array) {
      let currentIndex = array.length,  randomIndex;

      // While there remain elements to shuffle.
      while (currentIndex != 0) {

        // Pick a remaining element.
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex--;

        // And swap it with the current element.
        [array[currentIndex], array[randomIndex]] = [
          array[randomIndex], array[currentIndex]];
      }

      return array;
   }
}

class JSServerUtils {
   static addOnLoadFunction(func) {
      window.addEventListener("load", func);
   }

   static addClickFunctionOnId(id, func) {
      let el = document.getElementById(id);
      if (el != null) {
         el.addEventListener("click", func);
      }
   }

    static addClickFunctionOnClasses(className, func) {
      [...document.getElementsByClassName(className)].forEach(function(el) {
         el.addEventListener('click', func);
      });
   }

   static onTemplatePage(templateName) {
      return document.getElementById('article-container').getAttribute('data-page-type') === templateName;
   }
}

/**
 *  Ug, animations are awesome but also the king of all time sinks.
 *
 * This js animation library works, sucks that it's js but kudos for how simple
 * it is to use: https://css-tricks.com/using-css-transitions-auto-dimensions/#aa-technique-3-javascript
 *
 * KISS; at least the mini articles don't just dissapear anymore.
 */
class AnimateUtils {
   static collapseSection(element) {
      // get the height of the element's inner content, regardless of its actual size
      var sectionHeight = element.scrollHeight;

      // temporarily disable all css transitions
      var elementTransition = element.style.transition;
      element.style.transition = '';

      // on the next frame (as soon as the previous style change has taken effect),
      // explicitly set the element's height to its current pixel height, so we
      // aren't transitioning out of 'auto'
      requestAnimationFrame(function() {
         element.style.height = sectionHeight + 'px';
         element.style.transition = elementTransition;

         // on the next frame (as soon as the previous style change has taken effect),
         // have the element transition to height: 0
         requestAnimationFrame(function() {
            element.style.height = 0 + 'px';
         });
      }.bind(this));

      // mark the section as "currently collapsed"
      element.setAttribute('data-collapsed', 'true');
   }

   static expandSection(element) {
      // get the height of the element's inner content, regardless of its actual size
      var sectionHeight = element.scrollHeight;

      // have the element transition to the height of its inner content
      element.style.height = sectionHeight + 'px';

      // when the next css transition finishes (which should be the one we just triggered)
      element.addEventListener('transitionend', function(e) {
         // remove this event listener so it only gets triggered once
         // Note: Google Debug console doesn't like `callee`, but the animations work fine so :shrug:
         element.removeEventListener('transitionend', arguments.callee);

         // remove "height" from the element's inline styles, so it can return to its initial value
         element.style.height = null;
      });

      // mark the section as "currently not collapsed"
      element.setAttribute('data-collapsed', 'false');
   }
}

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
   static funLimit = 3;
   // We could stack loops on top but it looks better to stop one interval before you start another
   static funLoop;
   static minSpeed = 1;
   static maxSpeed = 10;
   static maxInterval = 2000;
   static minInterval = 300;

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

   static setupFun() {
      this.setupOGColors();
      this.setupNoFun();
      // Change some colors
      this.randomColorFun(10)
      // Change colors after we've loaded resources. (lol js)
      JSServerUtils.addOnLoadFunction(e => this.randomColorFun(3));
      // Start building fun with the 'fun-button'
      JSServerUtils.addClickFunctionOnClasses('fun-btn', this.addFun.bind(this));
   }
}

FunUtils.setupFun(); // this is sorta hidden on purpose. 👍 for reading the source

/**
 * Always fun to have this. Thanks: https://gomakethings.com/how-to-create-a-konami-code-easter-egg-with-vanilla-js/
 */
class Konami {
   static pattern = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
   static current = 0;
   static count = 0;

   static keyHandler(event) {
      // If the key isn't in the pattern, or isn't the current key in the pattern, reset
      if (this.pattern.indexOf(event.key) < 0 || event.key !== this.pattern[this.current]) {
         this.current = 0;
         return;
      }

      // Update how much of the pattern is complete
      this.current++;

      // If complete, alert and reset
      if (this.pattern.length === this.current) {
         this.current = 0;
         FunUtils.funBoom(++this.count);
      }
   };

   static setup() {
      // Listen for keydown events
      document.addEventListener('keydown', this.keyHandler.bind(this), false);
   }
}

Konami.setup(); // Shhh no secrets here.

class MiniArticleList {
   static activeTag;
   static activeSort;

   // Setup sorting and filtering click events
   static setupEvents() {
      this.addTagFilteringEvent();
      this.setupSortEvent();
   }

   static addTagFilteringEvent() {
      this.getAllTagBtns().forEach(btn => {
         btn.addEventListener('click', function handleClick(event) {
            // Figure out what mini article "tag" the page is filtering on
            let tag = event.target.getAttribute('data-value');
            this.filterMiniArticlesByTag(tag);
         }.bind(this)); // We're gonna call local logic so bind "this" up in scope
      });
   }

   static setupSortEvent() {
      this.getAllSortOptions().forEach(span => {
         span.addEventListener('click', function handleClick(event) {
            let oSpan = event.target;
            // Grab the sort order from the element
            let order = oSpan.getAttribute('data-sort');
            // Bail if we already sorted this way
            if (order === this.activeSort) {
               return;
            }
            this.activeSort = order;
            // Note: This is a bit of a js hack to keep 1 element selected:
            // 1. Remove all exiting "active" spans
            this.getAllSortOptions().forEach(el => {el.classList.remove('active')});
            // 2. Add it back to the one we care about
            oSpan.classList.add('active');
            this.orderArticles(order);
         }.bind(this)); // We're gonna call local logic so bind "this" up in scope
      });
   }

   static getAllSortOptions() {
      return document.querySelectorAll('#mini-article-sort-container span[data-sort]');
   }

   // This is pure JS DOM magic...
   static orderArticles(order) {
      // Grab the container holding all the possible article entries.
      let maEntryContainer = document.getElementById('mini-article-entries');
      // Grab all the possible entries in that container.
      let maArticles = maEntryContainer.getElementsByClassName('ma-entry-container');
      // Turn the htmlCollection into an Array
      let maArray = [...maArticles];
      maArray.sort(function(entryA, entryB) {
         // We'll assume only ascending and descending order
         let ascOrder = order === 'asc';
         function pullEndDateFromEntry(entryEl, startDate) {
            let endDateEl = entryEl.getElementsByClassName('ma-end-date')[0];
            if (!endDateEl) {
               return startDate;
            }
            return endDateEl.getAttribute('data-end-date');
         };
         // Brittle but we have a specific html structure so we know this is the DOM path to data-start-date
         let sDateA = entryA.getElementsByClassName('ma-start-date')[0].getAttribute('data-start-date');
         let eDateA = pullEndDateFromEntry(entryA, sDateA);

         let sDateB = entryB.getElementsByClassName('ma-start-date')[0].getAttribute('data-start-date');
         let eDateB = pullEndDateFromEntry(entryB, sDateB);

         // If we don't have an end date, use the start date.
         eDateA = eDateA ? eDateA : sDateA;
         eDateB = eDateB ? eDateB : sDateB;

         // If we're ascending, use the start dates, otherwise use the end dates.
         let dateA = ascOrder ? sDateA : eDateA;
         let dateB = ascOrder ? sDateB : eDateB;

         // We can do an int sort b/c start-date is a timestamp
         return ascOrder ? (dateA - dateB) : (dateB - dateA);
      });
      // Now that the articles have been re-ordered, go through them all & stick them back in the container
      maArray.forEach(el => {maEntryContainer.appendChild(el);});
   }

   static getAllTagBtns() {
      return document.querySelectorAll('#mini-article-tag-list .ma-tag');
   }

   static markActiveTag(newFilterTag) {
      if (this.activeTag === newFilterTag) {
         this.activeTag = null;
      } else {
         this.activeTag = newFilterTag;
      }
      this.getAllTagBtns().forEach(btn => {
         if (btn.getAttribute('data-value') === this.activeTag) {
            btn.classList.add("active");
         } else {
            btn.classList.remove("active");
         }
      }, this);
   }

   static filterMiniArticlesByTag(newFilterTag) {
      this.markActiveTag(newFilterTag);
      // Go through all the mini articles
      let miniArticles = document.querySelectorAll('#mini-article-entries .ma-entry-container');
      miniArticles.forEach(mArticle => {
         // Check through all the mini article's tags
         let tags = mArticle.getElementsByClassName('ma-entry-tags');
         let hasTag = false;
         // See if any match the "active" tag
         for (let i = 0; i < tags.length; i++) {
            if (tags[i].getAttribute('data-value') === this.activeTag) {
               hasTag = true;
            }
         }
         let $isCollapsed = mArticle.getAttribute('data-collapsed') === 'true';
         // If there's no filter or the filter matches the mini article tag, remove "hidden" class
         if (!this.activeTag || hasTag) {
            if ($isCollapsed) {
               AnimateUtils.expandSection(mArticle);
               mArticle.setAttribute('data-collapsed', 'false');
            }
         // Otherwise it's "filtered out" and we want to add the "hidden" class
         } else {
            if (!$isCollapsed) {
               AnimateUtils.collapseSection(mArticle);
            }
         }
      });
   }
}

MiniArticleList.setupEvents();

class Nav {
   static setupEvents() {
      [...document.querySelectorAll('nav div')].forEach(function(el) {
         el.addEventListener('click', function handleClick(event) {
            let aEl = el.getElementsByTagName('a')[0];
            aEl.click();
         }, this);
      });
   }
}

Nav.setupEvents();

class HomePage {
   static setupEvents() {
      [...document.querySelectorAll('a[href="#toggleParser"]')].forEach(function(el) {
         el.addEventListener('click', function handleClick(event) {
            event.preventDefault();
            let parsedContainer = document.getElementById('parsed-main-article-container');
            let rawTextContainer = document.getElementById('raw-text-main-article-container');
            parsedContainer.classList.toggle('hidden');
            rawTextContainer.classList.toggle('hidden');
         }, this);
      });
   }
}

// Should match PageIndex::HOMEPAGE_TYPE
if (JSServerUtils.onTemplatePage('homepage')) {
   HomePage.setupEvents();
}
