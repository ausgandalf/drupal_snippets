import _debounce from "lodash.debounce";

const mobileMQ = window.matchMedia("(max-width: 1024px)");

export class How_we_work {
  private element: HTMLElement;
  private elementNavNext: HTMLElement;
  private elementNavPrevious: HTMLElement;
  private elementContainer: HTMLElement;
  private elementIcon: HTMLElement;

  constructor(element: HTMLElement) {
    if (!!element) {
      this.element = element;
      this.elementNavNext = this.element.querySelector(
        ".how-we-work__nav-next"
      ) as HTMLElement;
      this.elementNavPrevious = this.element.querySelector(
        ".how-we-work__nav-previous"
      ) as HTMLElement;
      this.elementContainer = this.element.querySelector(
        ".how-we-work__container"
      )
      let all_icons = this.element.querySelectorAll('.how-we-work_icon') as NodeListOf<HTMLElement>;
      if (all_icons.length > 0) {
        this.elementIcon = all_icons[all_icons.length - 1]
        this.addCarouselEventlisteners();
      }
    }
    // const hwwNavLeft = document.querySelector(".how-we-work__nav-previous") as HTMLElement;
    // const hwwNavRight = document.querySelector(".how-we-work__nav-next") as HTMLElement;
    // const hwwContainer = document.querySelector(".how-we-work__container") as HTMLElement;

  }
  private addCarouselEventlisteners() {
    // onClick
    this.elementNavNext.addEventListener("click", (event) => {
      this.elementContainer.scrollLeft += 250;
    });

    this.elementNavPrevious.addEventListener("click", (event) => {
      this.elementContainer.scrollLeft -= 250;
    });

    let lastKnownScrollPosition: number = 0;
    let ticking: boolean = false;
    window.addEventListener("scroll", e => {
      lastKnownScrollPosition = this.elementContainer.scrollLeft;
      if (!ticking) {
        window.requestAnimationFrame(() => {
          animate(lastKnownScrollPosition);
          ticking = false;
        });
        ticking = true;
      }
    }, true);
    const scrollNextBtn = this.elementNavNext;
    const scrollPrevBtn = this.elementNavPrevious;
    const containerWidth = this.elementContainer.getBoundingClientRect().width;
    const icon6 = this.elementIcon.getBoundingClientRect().x;
    function animate(scrollPos: any) {
      if(scrollPos + containerWidth - 115 > icon6){
        scrollNextBtn.classList.add("disabled");
      } else if(scrollPos + containerWidth -115 < icon6){
        scrollNextBtn.classList.remove("disabled");
      };
      if(scrollPos === 0){
        scrollPrevBtn.classList.add("disabled");
      } else if(scrollPos > 0){
        scrollPrevBtn.classList.remove("disabled");
      };

    }
  }
}
export default function howWeWorkInit() {
  new How_we_work(document.querySelector(".how-we-work"));
}
