class MiniArticleList {
   static activeTag;
   static activeSort;

   // Setup sorting and filtering click events
   static setupEvents() {
      PageUtils.loadClass('AnimateUtils', function() {
         this.addTagFilteringEvent();
         this.setupSortEvent();
      }.bind(this));
   }

   static addTagFilteringEvent() {
      this.getAllTagBtns().forEach(btn => {
         btn.addEventListener('click', function handleClick(event) {
            let tagTarget = event.target;
            // Figure out what mini article "tag" the page is filtering on
            let tag = tagTarget.getAttribute('data-value');
            this.filterMiniArticlesByTag(tag);
            if (tagTarget.getAttribute('header-tag') !== null) {
               location.href = '#mini-article-list';
            }
         }.bind(this)); // We're gonna call local logic so bind "this" up in scope
      });
   }

   static setupSortEvent() {
      this.getAllSortOptions().forEach(span => {
         span.addEventListener('click', function handleClick(event) {
            let oSpan = event.target;
            // Grab the sort order from the element
            let order = oSpan.getAttribute('data-sort');
            // Bail if we already sorted this way
            if (order === this.activeSort) {
               return;
            }
            this.activeSort = order;
            // Note: This is a bit of a js hack to keep 1 element selected:
            // 1. Remove all exiting "active" spans
            this.getAllSortOptions().forEach(el => {el.classList.remove('active')});
            // 2. Add it back to the one we care about
            oSpan.classList.add('active');
            this.orderArticles(order);
         }.bind(this)); // We're gonna call local logic so bind "this" up in scope
      });
   }

   static getAllSortOptions() {
      return document.querySelectorAll('#mini-article-sort-container span[data-sort]');
   }

   // This is pure JS DOM magic...
   static orderArticles(order) {
      // Grab the container holding all the possible article entries.
      let maEntryContainer = document.getElementById('mini-article-entries');
      // Grab all the possible entries in that container.
      let maArticles = maEntryContainer.getElementsByClassName('ma-entry-container');
      // Turn the htmlCollection into an Array
      let maArray = [...maArticles];
      maArray.sort(function(entryA, entryB) {
         // We'll assume only ascending and descending order
         let ascOrder = order === 'asc';
         function pullEndDateFromEntry(entryEl, startDate) {
            let endDateEl = entryEl.getElementsByClassName('ma-end-date')[0];
            if (!endDateEl) {
               return startDate;
            }
            return endDateEl.getAttribute('data-end-date');
         };
         // Brittle but we have a specific html structure so we know this is the DOM path to data-start-date
         let sDateA = entryA.getElementsByClassName('ma-start-date')[0].getAttribute('data-start-date');
         let eDateA = pullEndDateFromEntry(entryA, sDateA);

         let sDateB = entryB.getElementsByClassName('ma-start-date')[0].getAttribute('data-start-date');
         let eDateB = pullEndDateFromEntry(entryB, sDateB);

         // If we don't have an end date, use the start date.
         eDateA = eDateA ? eDateA : sDateA;
         eDateB = eDateB ? eDateB : sDateB;

         // If we're ascending, use the start dates, otherwise use the end dates.
         let dateA = ascOrder ? sDateA : eDateA;
         let dateB = ascOrder ? sDateB : eDateB;

         // We can do an int sort b/c start-date is a timestamp
         return ascOrder ? (dateA - dateB) : (dateB - dateA);
      });
      // Now that the articles have been re-ordered, go through them all & stick them back in the container
      maArray.forEach(el => {maEntryContainer.appendChild(el);});
   }

   static getAllTagBtns() {
      return document.querySelectorAll('.ma-tag');
   }

   static markActiveTag(newFilterTag) {
      if (this.activeTag === newFilterTag) {
         this.activeTag = null;
      } else {
         this.activeTag = newFilterTag;
      }
      this.getAllTagBtns().forEach(btn => {
         if (btn.getAttribute('data-value') === this.activeTag) {
            btn.classList.add("active");
         } else {
            btn.classList.remove("active");
         }
      }, this);
   }

   static filterMiniArticlesByTag(newFilterTag) {
      this.markActiveTag(newFilterTag);
      // Go through all the mini articles
      let miniArticles = document.querySelectorAll('#mini-article-entries .ma-entry-container');
      miniArticles.forEach(mArticle => {
         // Check through all the mini article's tags
         let tags = mArticle.getElementsByClassName('ma-entry-tags');
         let hasTag = false;
         // See if any match the "active" tag
         for (let i = 0; i < tags.length; i++) {
            if (tags[i].getAttribute('data-value') === this.activeTag) {
               hasTag = true;
            }
         }
         let $isCollapsed = mArticle.getAttribute('data-collapsed') === 'true';
         // If there's no filter or the filter matches the mini article tag, remove "hidden" class
         if (!this.activeTag || hasTag) {
            if ($isCollapsed) {
               AnimateUtils.expandSection(mArticle);
               mArticle.setAttribute('data-collapsed', 'false');
            }
         // Otherwise it's "filtered out" and we want to add the "hidden" class
         } else {
            if (!$isCollapsed) {
               AnimateUtils.collapseSection(mArticle);
            }
         }
      });
   }
}