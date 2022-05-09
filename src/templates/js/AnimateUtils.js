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