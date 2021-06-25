// Create a fixed drag container for the dragged items, this is done with JS
// just for example's purposes.
var dragContainer = document.createElement("div");
dragContainer.style.position = "fixed";
dragContainer.style.left = "0px";
dragContainer.style.top = "0px";
dragContainer.style.zIndex = 1000;
document.body.appendChild(dragContainer);

//
// GRID
//
const gridElement = document.querySelector(".grid");
var grid = new Muuri(gridElement, {
  showDuration: 400,
  showEasing: "ease",
  hideDuration: 400,
  hideEasing: "ease",
  layoutDuration: 400,
  layoutEasing: "cubic-bezier(0.625, 0.225, 0.100, 0.890)",
  layout: {
    fillGaps: false,    
  },
  dragEnabled: true,
  dragContainer: dragContainer,
  dragStartPredicate: {
    distance: 100,
    delay: 100
  },
  dragSortPredicate: {
    action: "move",
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
}).on("layoutStart", function (items) {
  items.forEach(function (item) {
    item.getElement().style.width = item.getWidth() + "px";
    item.getElement().style.height = item.getHeight() + "px";
  });
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
