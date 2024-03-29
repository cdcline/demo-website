class PageUtils {
   static jsFilePath = 'src/templates/js/';
   static loadedFiles = [];

   static runLoader() {
      // We don't really wanna start running our JS until we have all the elements
      // on the page.
      document.addEventListener("DOMContentLoaded", this.setupEvents.bind(this));
   };

   /**
    * Gonna add a wrapper around loading so we can call "loadClass" in lots of
    * spots.
    *
    * Note: Assumes the className will match the file name.
    */
   static loadClass(className, callFunc, waitForMoreToLoad) {
      let filename = this.jsFilePath + className + '.js';
      // If we've loaded the class already
      if (this.classLoaded(className)) {
         // Fire off the callback function
         if (callFunc) {
            callFunc();
         }
         return;
      }

      // Otherwise after loading the file, mark class loaded & fire off callback
      let loadedFunc = function() {
         this.markClassLoaded(className);
         callFunc();
      }.bind(this);
      loadJS(filename, loadedFunc, waitForMoreToLoad);
   }

   static classLoaded(className) {
      return this.loadedFiles[className];
   };

   // I'm sure there's a better way of doing this but this works in our small case.
   static markClassLoaded(className) {
      this.loadedFiles[className] = 'loaded';
   }

   // Techically loading all these different js files is bad form b/c it makes a lot
   // of server requests.
   //
   // Practially for this demo the number of requests and sizes are so low it doesn't
   // make any noticable difference in performance.
   //
   // For now this is just a fun example of dynamic JS loading.
   //
   // If (for whatever reason) performance becomes an issue, we can look into bundling libraries
   static setupEvents() {
      // This is needed on every page and probably SHOULD be in this file but this one is too large and complex already.
      // We'll Look into Bundles if we really care.
      this.loadClass('Nav', function() {Nav.setupEvents()});

      // Page JS loading
      // We're gonna need server utils to check what page we're on.
      this.loadClass('JSServerUtils', function() {
         // Should match PageIndex::HOMEPAGE_TYPE
         if (JSServerUtils.onTemplatePage('homepage')) {
            this.loadClass('HomePage', function() {
               HomePage.setupEvents();
            }.bind(this));
         }

         this.loadClass('MathUtils', function() {
            // Check for Welcome Header
            if (document.getElementById('welcome-header-container')) {
               PageUtils.loadClass('WelcomeHeader', function() {
                  WelcomeHeader.init();
               });
            }

            // Check for Robots Header
            if (document.getElementById('robots-header-container')) {
               PageUtils.loadClass('RobotsHeader', function() {
                  RobotsHeader.init();
               });
            }

            // Check for Work Header
            if (document.getElementById('work-header-container')) {
               PageUtils.loadClass('WorkHeader', function() {
                  WorkHeader.init();
               });
            }

            // Check for Block'O'Fun
            if (document.getElementsByClassName('fun-btn').length) {
               PageUtils.loadClass('FunUtils', function() {
                  FunUtils.setupFun();
               });
            }
         });
      }.bind(this));

      // Check for MiniArticleList
      if (document.getElementById('mini-article-list')) {
         PageUtils.loadClass('MiniArticleList', function() {
            MiniArticleList.setupEvents();
         });
      }

      // Check for CarouselController
      if (document.getElementById('carousel-controller')) {
         PageUtils.loadClass('CarouselController', function() {
            CarouselRunner.init();
         });
      }
   }
}

PageUtils.runLoader();

/**
 * This is the kinda hacky way we support splitting up the js files.
 *
 * JS is the new hotness and reducing the size of complex JS code is a big thing.
 *
 * We however are writing super vanilla, boring js code that's 1/10000 the size that
 * we need to worry about. However, using a single JS file for our code is getting
 * unmanagable just due to the length & mixed scope of the file.
 *
 * Ideally we have 1 file for the user to download with all the JS they'd need
 * for the page an nothing more.
 *
 * Modern JS libraries (React, Node) do this all for you in the background and
 * you just add simple "import" statements when you need to use outside code.
 *
 * Oldschool JS would create "bundles" and figure out what code is called in what
 * places and would optimize the files to be the minimum needed to load each page.
 *
 * Both would do what we want but both have major downsides:
 *  - React and Node are a whole framework. While fun & relevant; not what we want in this project.
 *   - We'd have started with it if we wated this. Converting would take a lot of time.
 *  - "Bundles" require either some compiler or some specific framework stuff.
 *   - We don't really want to add another library & process to deploy things
 *
 * So we get to this solution. A script written to load resources "as needed."
 *
 * This is terribly in-efficient if we were doing a large app. It will
 *  - Do a request per js file
 *   - Asking to load "more things" later is generally slower
 *  - Not do any optimizing
 *   - Lots of things can be done to minimize js code automagically. We're doing none of it.
 *  - Still load most all the things
 *   - We basically want to load all the JS on all the pages. What we can not load is a very, very small size.
 *
 * However:
 *  - We can easily split up JS files
 *  - We have a "asynchronous load" function
 *   - Mostly going to use this to demo HOW you'd use it not necessarily for the actual result
 *  - It works with just this simple script
 *   - JS management can get really complex but we don't want it complex.
 *   - Just need it for basic logic & scope splitting.
 */
/*! loadJS: load a JS file asynchronously. [c]2014 @scottjehl, Filament Group, Inc. (Based on http://goo.gl/REQGQ by Paul Irish). Licensed MIT */
(function( w ){
	var loadJS = function( src, cb, ordered ){
		"use strict";
		var tmp;
		var ref = w.document.getElementsByTagName( "script" )[ 0 ];
		var script = w.document.createElement( "script" );

		if (typeof(cb) === 'boolean') {
			tmp = ordered;
			ordered = cb;
			cb = tmp;
		}

		script.src = src;
		script.async = !ordered;
		ref.parentNode.insertBefore( script, ref );

		if (cb && typeof(cb) === "function") {
			script.onload = cb;
		}
		return script;
	};
	// commonjs
	if( typeof module !== "undefined" ){
		module.exports = loadJS;
	}
	else {
		w.loadJS = loadJS;
	}
}( typeof global !== "undefined" ? global : this ));

/**
 * Always fun to have this. Thanks: https://gomakethings.com/how-to-create-a-konami-code-easter-egg-with-vanilla-js/
 */
class Konami {
   static pattern = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
   static current = 0;
   static count = 0;

   static keyHandler(event) {
      // If the key isn't in the pattern, or isn't the current key in the pattern, reset
      if (this.pattern.indexOf(event.key) < 0 || event.key !== this.pattern[this.current]) {
         this.current = 0;
         return;
      }

      // Update how much of the pattern is complete
      this.current++;

      // If complete, alert and reset
      if (this.pattern.length === this.current) {
         this.current = 0;
         PageUtils.loadClass('FunUtils', function() {
            let funSpeed = ++this.count;
            FunUtils.setupFun(function() {
               FunUtils.funBoom(funSpeed);
            });
         }.bind(this));
      }
   };

   static setup() {
      // Listen for keydown events
      document.addEventListener('keydown', this.keyHandler.bind(this), false);
   }
}

Konami.setup(); // Shhh no secrets here.