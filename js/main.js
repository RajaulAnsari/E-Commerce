const navList = document.querySelector(".navbar-list");

document.querySelector(".fourline").onclick = () => {
  navList.classList.add("show");
};

document.querySelector(".close").onclick = () => {
  navList.classList.remove("show");
};
