const toggler = document.querySelector(".toggler-btn");
toggler.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("collapsed");
});

function navBarMenuActive(menuName, subMenuName){
  
  if (typeof(Storage) !== "undefined") {
      sessionStorage.setItem("activeMenuName", menuName);
      sessionStorage.setItem("activeSubMenuName", subMenuName);
  }
}
if(typeof(Storage) !== "undefined") {

      // var activeMenuName = sessionStorage.getItem('activeMenuName');
      // var activeSubMenuName = sessionStorage.getItem('activeSubMenuName');
      // $("#p"+activeMenuName).attr("aria-expanded","true");
      // $("#"+activeMenuName).addClass("show");
      // console.log($("#"+activeMenuName));
      // $("#ul_"+activeSubMenuName).addClass("active-link");
}