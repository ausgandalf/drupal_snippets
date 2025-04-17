import _debounce from "lodash.debounce";

const mobileMQ = window.matchMedia("(max-width: 1024px)");

export class InteriorHero {
  private element: HTMLElement;
  private elementHero: HTMLElement;
  private elementArc: HTMLElement;
  private elementBuilding: HTMLElement;
  private elementIntBuilding: HTMLElement;

  constructor(element: HTMLElement) {
    if (!!element) {
      this.element = element;
      this.elementIntBuilding = document.querySelector(".interior-hero__building") as HTMLElement;
      this.parallaxAnimation();
    }
  }

  private parallaxAnimation(){
    const intBuilding = this.elementIntBuilding;
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
      if(intBuilding){
        intBuilding.style.bottom = scrollPos * 0.01 + -10 + "rem";
      }
    }
  }
}
export default function intHeroInit() {
  new InteriorHero(document.querySelector(".interior-hero"));
}
