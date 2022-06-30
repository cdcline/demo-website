/**
 * There are 2 different carousels with different dimensions for "mobile" and "full" width.
 *
 * Keeping them in sync isn't really needed but it's more fun. This runner is how we handle
 * the general state for both of the carousels.
 *
 * It mostly just manages the events and carousel objects.
 */
const CarouselRunner = {
   carousels: [],
   numSlides: 0,
   position: 1,

   init: function() {
      document.querySelectorAll(".js-flex-carousel").forEach(function(slideshowEl) {
         this.carousels.push(new FlexCarousel(slideshowEl));
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
         el.setActive(this.position, true);
      }.bind(this));
   },

   gotoPrev: function() {
      this.position--;
      this.normalizePosition();
      this.carousels.forEach(function(el) {
         el.setActive(this.position, false);
      }.bind(this));
   },

   normalizePosition: function() {
      // We don't expect invalid positions and want to wrap to the first slide
      // so if "next position" is "over" we go back to the first slide.
      if (this.position > this.numSlides) {
         this.position = 1;
      // We can go back so if we go too far back, wrap to the end slide
      } else if (this.position < 1) {
         this.position = this.numSlides;
      }
   }
};

/**
 * We'd like to have a "Sliding Carousel of Images" in the header.
 *
 * The Carousel supports a controller that allows the user to go to the next and
 * previous slides and also to view a specific slide by clicking a button.
 *
 * To make our carousel cool, we will animate "previous" and "next" in two
 * directions, left and right. If the user "sets" a slide, we will move in
 * the direction of the slide relative to the currently visible one.
 *
 * To actually animate this, we have to do a lot of silly css and js magic.
 *
 * Started from: https://usefulangle.com/post/313/css-flex-order-carousel-infinite
 */
class FlexCarousel {
	constructor(slideshowEl) {
      this.current = 1;
      this.movingTo = null;
      this.container = slideshowEl;
      this.slides = this.container.getElementsByClassName("js-carousel-slide");
      this.num_items = this.slides.length;
		// set CSS order of each item initially
      [...this.slides].forEach(function(element, index) {
         element.style.order = index+1;
      });

		// after each item slides in, slider container fires transitionend event
		this.container.addEventListener('transitionend', function(el) {
         this.cleanupTransition();
      }.bind(this));
	}

   numSlides() {
      return this.num_items;
   }

   setActive(position, goingNext) {
      let invalidPosition = position < 1 || position > this.num_items;
      let isVisible = this.current == position;
      if (invalidPosition || isVisible || this.inTransition()) {
         return;
      }
      this.manualNext = (typeof goingNext !== 'undefined') ? goingNext : null;
      this.movingTo = position;
      this.setupSlidesForTransition();
      this.animateSlideTransition();
   }

   inTransition() {
      return this.movingTo !== null;
   }

   /**
    * We have to do so CSS hijinx to setup the carousel to animate moving to the
    * next or previous slide.
    *
    * Going left or right require different CSS hijinx.
    */
   setupSlidesForTransition() {
      if (this.goingNext()) {
         this.setupSlideRight();
      } else {
         this.setupSlideLeft();
      }
   }

   /**
    * We allow manually going "next" but if we're setting a specific position
    * we go "next" if it's in a later order position.
    */
   goingNext() {
      return this.manualNext !== null ? this.manualNext : this.movingTo > this.current;
   }

   setupSlideRight() {
      [...this.slides].forEach(function(slide) {
         // We use "position" to id the slides
         let slideId = parseInt(slide.getAttribute('data-position'));
         // The "order" will be dynamic, depending on what slide is shown.
         let slideCurrentOrder = slide.style.order;
         // Move the slide we're showing "next" into the 2nd position
         if (slideId == this.movingTo) {
            slide.style.order = 2;
         // Move the exisiting 2nd position slide elsewhere
         } else if (slideCurrentOrder == 2) {
            // Doesn't doesn't matter where we move it b/c we re-order at the end of the animation
            slide.style.order = this.num_items + 1;
         }
      }.bind(this));
   }

   setupSlideLeft() {
      let sIndex = 3;
      [...this.slides].forEach(function(slide) {
         // We use "position" to id the slides
         let slideId = parseInt(slide.getAttribute('data-position'));
         // Find the slide we want to move left to
         if (slideId == this.movingTo) {
            // Stick it in the "first" slot
            slide.style.order = 1;
         // Find the slide that's currently visible
         } else if (slide.style.order == 1) {
            // Stick it in the 2nd slot
            slide.style.order = 2;
         } else {
            // Move all the remaining slides after the 2nd slot
            slide.style.order = sIndex++;
         }
      }.bind(this));
      // Add the css trick to view the slide in the 2nd order slot instead of the 1st one
      this.container.classList.add('slider-container-transition-down');
   }

   /**
    * Our CSS animation trick involves transforming the X position and playing
    * with the order of elements.
    *
    * After we've done the animation, we want to clean things up the state of things
    * so we can repeat any number of times.
    *
    * This resets the carousel back to the "initial load" layout but with the
    * style.order changed to reflect the currently "visible" slide.
    */
	cleanupTransition() {
      // Make sure to remove the watchdog timeout
      if (this.timeout) {
         clearTimeout(this.timeout);
      }

      // We might call this a lot.
      // We only really want to cleanup if we're finishing a transition.
      if (!this.inTransition()) {
         return;
      }

      // Reorder the slides so that that "this.current" slide is style.order 1
      // and all the following slides are in ascending order.
      [...this.slides].forEach(function(slide) {
         let ogOrder = parseInt(slide.getAttribute('data-position'));
         slide.style.margin = 0;
         let newOrder = ogOrder - (this.movingTo - 1);
         if (newOrder < 1) {
            newOrder = newOrder + this.num_items;
         }
         slide.style.order = newOrder;
      }.bind(this));

      // We don't want transitionend to fire for this translation, so remove transition CSS
      this.container.classList.remove('slider-container-transition');
      // We want to show the slides "normally" so remove all the transition css stuff
      this.container.classList.remove('slider-container-transition-down');
      this.container.style.transform = 'translateX(0)';

      // We've moved and updated styles so reset the initial state
      this.current = this.movingTo;
      this.movingTo = null;
	}

	animateSlideTransition() {
      // translate from 0 to -100%
      // we need transitionend to fire for this translation, so add transition CSS
      this.container.classList.add('slider-container-transition');
      let transform = this.goingNext() ? 'translateX(-100%)' : 'translateX(100%)';
      this.container.style.transform = transform;
      // The transition end doesn't fire if it's not visible so we'll fire the cleanup
      // manually after the transition should have happened.
      this.timeout = setTimeout(this.cleanupTransition.bind(this), 1000);
	}
};
