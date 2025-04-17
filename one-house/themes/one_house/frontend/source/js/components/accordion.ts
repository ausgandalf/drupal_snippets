const Accordion = require("accordion-js");

export class AccordionJS {
  private element: HTMLElement;
  private cta: HTMLAnchorElement;

  constructor(element: HTMLElement) {
    if (!!element) {
      this.element = element;
      this.init();
    }
  }

  private init() {
    new Accordion(this.element, {
      duration: 100,
      elementClass: "accordion__accordion",
      triggerClass: "accordion__trigger",
      panelClass: "accordion__content",
      ariaEnabled: true,
    });
  }
}

export default function accordionInit() {
  const accordions = document.querySelectorAll(
    ".accordion"
  ) as NodeListOf<HTMLElement>;
  for (let i = 0; i < accordions.length; i++) {
    new AccordionJS(accordions[i]);
  }
}
