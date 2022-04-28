// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/random#getting_a_random_integer_between_two_values_inclusive
function getRandomIntInclusive(min, max) {
   min = Math.ceil(min);
   max = Math.floor(max);
   return Math.floor(Math.random() * (max - min + 1) + min); //The maximum is inclusive and the minimum is inclusive
}

function getRandomColor() {
   return getRandomIntInclusive(0, 255);
}

function getRandomRGB() {
   let r = getRandomColor();
   let g = getRandomColor();
   let b = getRandomColor();
   return "rgb(" + r + "," + g + "," + b + ")";
}

function makeFunColors() {
   const collection = document.getElementsByClassName("fun");
   for (let i = 0; i < collection.length; i++) {
      collection[i].style.color = getRandomRGB();
   }
}

makeFunColors();
document.getElementById("fun-button").addEventListener("click", makeFunColors);