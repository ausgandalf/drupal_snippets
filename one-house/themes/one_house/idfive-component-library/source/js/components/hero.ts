import _debounce from "lodash.debounce";

const mobileMQ = window.matchMedia("(max-width: 1024px)");

export class Hero {
  private element: HTMLElement;
  private elementHero: HTMLElement;
  private elementBuilding: HTMLElement;
  private elementIntBuilding: HTMLElement;

  constructor(element: HTMLElement) {
    if (!!element) {
      this.element = element;
      this.elementHero = document.querySelector(".hero-with-image") as HTMLElement;
      this.elementBuilding = document.querySelector(".hero__building") as HTMLElement;
      this.parallaxAnimation();
    }
  }
  private parallaxAnimation(){
    const hero = this.elementHero;
    const building = this.elementBuilding;
    let lastKnownScrollPosition: number = 0;
    let ticking: boolean = false;
    window.addEventListener("scroll", e => {
      lastKnownScrollPosition = window.scrollY;
      if (!ticking) {
        window.requestAnimationFrame(() => {
          animate(lastKnownScrollPosition); 
          ticking = false;
        });
        ticking = true;
      }
    }, true);
    
    function animate(scrollPos: any) {
      hero.style.backgroundPositionX = scrollPos * 0.1 + "px";
      building.style.top = -scrollPos * 0.02 + 17.1875 + "rem";
    }
  }
}
export default function heroInit() {
  new Hero(document.querySelector(".hero"));
}
