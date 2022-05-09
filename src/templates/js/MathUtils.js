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