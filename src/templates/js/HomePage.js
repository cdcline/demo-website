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