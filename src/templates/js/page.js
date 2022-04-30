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
      let tagFilterBtns = document.querySelectorAll('#mini-article-tag-list ul li');

      tagFilterBtns.forEach(btn => {
         btn.addEventListener('click', function handleClick(event) {
            this.filterMiniArticlesByTag(event.target);
         }.bind(this)); // We're gonna call local logic so bind "this" up in scope
      });
   }

   static filterMiniArticlesByTag(el) {
      // Figure out what mini article "tag" the page is filtering on
      let newFilterTag = el.getAttribute('data-value');
      if (this.activeTag === newFilterTag) {
         this.activeTag = null;
      } else {
         this.activeTag = newFilterTag;
      }

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
         // If there's no filter or the filter matches the mini article tag, remove "hidden" class
         if (!this.activeTag || hasTag) {
            mArticle.classList.remove("hidden");
         // Otherwise it's "filtered out" and we want to add the "hidden" class
         } else {
            mArticle.classList.add("hidden");
         }
      });
   }
}
MiniArticleList.setupEvents();