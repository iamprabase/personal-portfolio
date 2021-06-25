const dragContainer = document.querySelector(".drag-container");
const gridElement = document.querySelector(".grid");
//
// GRID
//

var grid = new Muuri(gridElement, {
  showDuration: 400,
  showEasing: "ease",
  hideDuration: 400,
  hideEasing: "ease",
  layoutDuration: 400,
  layoutEasing: "cubic-bezier(0.625, 0.225, 0.100, 0.890)",
  layout: {
    fillGaps: true,
  },
  dragEnabled: true,
  dragHandle: ".grid-card-handle",
  dragContainer: dragContainer,
  dragSortPredicate: {
    action: 'swap',    
  },
  dragRelease: {
    duration: 400,
    easing: "cubic-bezier(0.625, 0.225, 0.100, 0.890)",
    useDragContainer: true,
  },
  dragPlaceholder: {
    enabled: true,
    createElement(item) {
      return item.getElement().cloneNode(true);
    },
  },
  dragAutoScroll: {
    targets: [window],
    sortDuringScroll: false,
    syncAfterScroll: false,
  },
});

// When all items have loaded refresh their
// dimensions and layout the grid.
window.addEventListener("load", function () {
  grid.refreshItems().layout();
  // For a little finishing touch, let's fade in
  // the images after all them have loaded and
  // they are corrertly positioned.
  document.body.classList.add("items-loaded");
});
