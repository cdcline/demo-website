class WorkHeader {
   static shape;

   static init() {
      this.shape = document.getElementById('header-curve');
      this.shape.classList.add('js-animate-curve');
      document.getElementById('work-header-background-container').addEventListener('transitionend', function(ev) {
         let isAnimation = ev.propertyName === 'background-position-x';
         if (isAnimation) {
            this.shape.classList.toggle('js-animate-curve');
         }
      }.bind(this));

   }
}