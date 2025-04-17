import _debounce from "lodash.debounce";

const mobileMQ = window.matchMedia("(max-width: 1024px)");

export class Properties {
  private element: HTMLElement;
  private elementSaleBtn: HTMLElement;
  private elementSoldBtn: HTMLElement;
  private elementSaleTab: HTMLElement;
  private elementSoldTab: HTMLElement;

  constructor(element: HTMLElement) {
    if (!!element) {
      this.element = element;
      this.elementSaleBtn = this.element.querySelector(
        "#tab__toggle__sale"
      ) as HTMLElement;
      this.elementSoldBtn = this.element.querySelector(
        "#tab__toggle__sold"
      ) as HTMLElement;
      this.elementSaleTab = this.element.querySelector(
        "#tab__sale"
      ) as HTMLElement;
      this.elementSoldTab = this.element.querySelector(
        "#tab__sold"
      ) as HTMLElement;
      this.addEventlisteners();
    }
    
  }
  private addEventlisteners() {
    // onClick
    this.elementSaleBtn.addEventListener("click", (event) => {
      this.elementSaleBtn.classList.add("active");
      this.elementSaleTab.style.display = "block";
      this.elementSaleTab.setAttribute("aria-hidden", "false");
      this.elementSoldBtn.classList.remove("active");
      this.elementSoldTab.style.display = "none";
      this.elementSoldTab.setAttribute("aria-hidden", "true");
    });

    this.elementSoldBtn.addEventListener("click", (event) => {
      this.elementSoldBtn.classList.add("active");
      this.elementSoldTab.style.display = "block";
      this.elementSoldTab.setAttribute("aria-hidden", "false");
      this.elementSaleBtn.classList.remove("active");
      this.elementSaleTab.style.display = "none";
      this.elementSaleTab.setAttribute("aria-hidden", "true");
    });
  }
}
export default function propertiesInit() {
  new Properties(document.querySelector(".tabs"));
}
