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
