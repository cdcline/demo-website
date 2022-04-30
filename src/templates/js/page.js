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
}

class ServerUtils {
   static addOnLoadFunction(func) {
      window.addEventListener("load", func);
   }

   static addClickFunctionOnId(id, func) {
      let el = document.getElementById(id);
      if (el != null) {
         el.addEventListener("click", func);
      }
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

class FunUtils {
   static makeFunColors() {
      let collection = document.getElementsByClassName("fun");
      for (let i = 0; i < collection.length; i++) {
         collection[i].style.color = MathUtils.getRandomRGB();
      }
   }

   static setupFun() {
      // Change colors before we've loaded resources
      this.makeFunColors()
      // Change colors after we've loaded resources
      ServerUtils.addOnLoadFunction(this.makeFunColors);
      // Change colors when we click the 'fun-button'
      ServerUtils.addClickFunctionOnId('fun-button', this.makeFunColors);
   }
}

FunUtils.setupFun();

class MiniArticleList {
   static activeTag;

   static setupEvents() {
      this.addTagFilteringEvent();
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

   static getAllTagBtns() {
      return document.querySelectorAll('#mini-article-tag-list ul li');
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
      let miniArticles = document.querySelectorAll('#mini-article-entries .mini-article-container');
      miniArticles.forEach(mArticle => {
         // Check through all the mini article's tags
         let tags = mArticle.getElementsByClassName('mini-article-tags');
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