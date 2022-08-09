class WorkHeader {
   static backgroundShape;

   static init() {
      this.backgroundShape = new BackgroundShape(document.getElementById('work-header-background-container'));
   }
}

class BackgroundShape {
   constructor(shapeContainer) {
      this.shapeContainer = shapeContainer;
      this.shape = this.shapeContainer.querySelector('.curve');
      // It's ugly to hardcode these but calculating it is a pain.
      this.colors = ['#8e2de2', '#4a00e0', '#8e2de2', '#4a00e0'];

      this.shapeContainer.addEventListener('transitionend', function(ev) {
         let isAnimation = ev.propertyName === 'background-position-x';
         if (isAnimation) {
            if (this.animatingRight) {
               this.animateLeft();
            } else {
               this.animateRight(/*resetColors*/true);
            }
         } else {

         }
      }.bind(this));

      this.animateRight(/*resetColors*/false);
   }

   animateRight(resetColors) {
      if (resetColors) {
         this.colors[2] = MathUtils.getRandomRGB();
         this.colors[3] = MathUtils.getRandomRGB();
      }
      this.shape.style.background = this.buildGrandiant();
      this.shape.classList.add('js-animate-curve');
      this.animatingRight = true;
   }

   animateLeft() {
      this.colors[0] = MathUtils.getRandomRGB();
      this.colors[1] = MathUtils.getRandomRGB();
      this.shape.style.background = this.buildGrandiant();
      this.shape.classList.remove('js-animate-curve');
      this.animatingRight = false;
   }

   buildGrandiant() {
      let colorStr = this.colors.join(', ');
      this.shape.style.backgroundImage = 'linear-gradient(to left, ' + colorStr + ')';
   }
}