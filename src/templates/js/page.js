class MathUtils {
   // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/random#getting_a_random_integer_between_two_values_inclusive
   static getRandomIntInclusive(min, max) {
      let min = Math.ceil(min);
      let max = Math.floor(max);
      return Math.floor(Math.random() * (max - min + 1) + min); //The maximum is inclusive and the minimum is inclusive
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
      document.getElementById(id).addEventListener("click", func);
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