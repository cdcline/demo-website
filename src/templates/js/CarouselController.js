const CarouselRunner = {
   carousels: [],
   numSlides: 0,
   position: 1,

   init: function() {
      document.querySelectorAll(".js-flex-carousel").forEach(function(slideshowEl) {
         this.carousels.push(new FlexSlider(slideshowEl));
      }.bind(this));

      if (this.carousels.length) {
         this.numSlides = this.carousels[0].numSlides();
      }

		document.querySelectorAll(".js-next-button").forEach(function(el) {
         el.addEventListener('click', this.gotoNext.bind(this));
		}.bind(this));

		document.querySelectorAll(".js-prev-button").forEach(function(el) {
         el.addEventListener('click', this.gotoPrev.bind(this));
		}.bind(this));

		document.querySelectorAll(".js-set-carousel-slide").forEach(function(el) {
         el.addEventListener('click', function(ev) {
            let position = ev.target.getAttribute('data-position');
            this.gotoPosition(position);
         }.bind(this))
		}.bind(this));
   },

   gotoPosition: function(position) {
      this.position = position;
      this.normalizePosition();
      this.carousels.forEach(function(el) {
         el.setActive(this.position);
      }.bind(this));
   },

   gotoNext: function() {
      this.position++;
      this.normalizePosition();
      this.carousels.forEach(function(el) {
         el.setActive(this.position);
      }.bind(this));
   },

   gotoPrev: function() {
      this.position--;
      this.normalizePosition();
      this.carousels.forEach(function(el) {
         el.setActive(this.position);
      }.bind(this));
   },

   normalizePosition: function() {
      if (this.position > this.numSlides) {
         this.position = 1;
      } else if (this.position < 1) {
         this.position = this.numSlides;
      }
   }

};

// Thanks https://usefulangle.com/post/313/css-flex-order-carousel-infinite
class FlexSlider {
	constructor(slideshowEl) {
      this.current = 1;
      this.container = slideshowEl;
      this.slides = this.container.getElementsByClassName("js-carousel-slide");
      this.num_items = this.slides.length;
		// set CSS order of each item initially
      [...this.slides].forEach(function(element, index) {
         element.style.order = index+1;
      });

		this.addEvents();
	}

   numSlides() {
      return this.num_items;
   }

   setActive(position) {
      if (this.inTransition()) {
         return;
      }
      if (this.current === position) {
         return;
      }
      [...this.slides].forEach(function(slide) {
         let initPosition = parseInt(slide.getAttribute('data-position'));
         if (slide.style.order === 2) {
            slide.style.order = this.num_items + 1;
         }
         if (position === initPosition) {
            slide.style.order = 2;
         }
      });
      this.current = position;
      this.showNextSlide();
   }

	addEvents() {
		// after each item slides in, slider container fires transitionend event
		this.container.addEventListener('transitionend', function(el) {
         this.changeOrder();
      }.bind(this));
	}

	changeOrder() {
      if (this.timeout) {
         clearTimeout(this.timeout);
      }
      // We only want to change the order if it has a "transition" to do
      if (!this.container.classList.contains('slider-container-transition')) {
         return;
      }

		// change current position. We start at 1
		if(this.current > this.num_items)
			this.current = 1;

      [...this.slides].forEach(function(slide) {
         let ogOrder = parseInt(slide.getAttribute('data-position'));
         let newOrder = ogOrder - (this.current - 1);
         if (newOrder < 1) {
            newOrder = newOrder + this.num_items;
         }
         slide.style.order = newOrder;

         // translate back to 0 from -100%
         // we don't need transitionend to fire for this translation, so remove transition CSS
         this.container.classList.remove('slider-container-transition');
         this.container.style.transform = 'translateX(0)';
      }.bind(this));
	}

	showNextSlide() {
      // translate from 0 to -100%
      // we need transitionend to fire for this translation, so add transition CSS
      this.container.classList.add('slider-container-transition');
      this.container.style.transform = 'translateX(-100%)';
      // The transition end doesn't fire if it's not visible so we'll fire the change
      // manually after the transition should have happened.
      this.timeout = setTimeout(this.changeOrder.bind(this), 1000);
	}

   inTransition() {
      return this.container.classList.contains('slider-container-transition');
   }
};

