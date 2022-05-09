class JSServerUtils {
   static addOnLoadFunction(func) {
      window.addEventListener("load", func);
   }

   static addClickFunctionOnId(id, func) {
      let el = document.getElementById(id);
      if (el != null) {
         el.addEventListener("click", func);
      }
   }

    static addClickFunctionOnClasses(className, func) {
      [...document.getElementsByClassName(className)].forEach(function(el) {
         el.addEventListener('click', func);
      });
   }

   static onTemplatePage(templateName) {
      return document.getElementById('article-container').getAttribute('data-page-type') === templateName;
   }
}