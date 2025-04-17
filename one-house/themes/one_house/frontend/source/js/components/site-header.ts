import _debounce from "lodash.debounce";
import AccessibilityUtilities from "../utilities/accessibility";

const mobileMQ = window.matchMedia("(max-width: 1200px)");

export class SiteHeader {
  private headertop: HTMLElement;
  private element: HTMLElement;
  private nav: HTMLElement;
  private scroller: HTMLElement;
  private openButton: HTMLButtonElement;
  private focusableChildren: NodeListOf<HTMLElement>;
  private parentNavItems: NodeListOf<HTMLElement>;
  private siteHeaderTop: HTMLElement;
  public visible: boolean = false;
  private eventHandlers: any = {
    show: [],
    hide: [],
  };

  constructor(element: HTMLElement) {
    this.headertop = document.querySelector('.site-header__top');

    if (!!element) {
      this.element = element;
      this.scroller = this.element.querySelector(
        ".site-header__mega-menu-scroller"
      );
      this.openButton = AccessibilityUtilities.convertAnchorToButton(
        document.querySelector(`[aria-controls="${this.element.id}"]`)
      );
      this.focusableChildren =
        this.element.querySelectorAll("a, button, input");
      this.nav = this.element.querySelector(".site-header__mega-menu-main-nav");
      this.parentNavItems = this.element.querySelectorAll(
        ".site-header__mega-menu-main-nav > ul > li"
      );
      this.siteHeaderTop = this.element.querySelector(".site-header__top");
      this.init();
    }
  }

  private init() {
    this.handleResize();
    this.handleEsc();
    this.handleTabbing();
    this.handleOpenButtonClick();
    this.handleTransitionEnd();
    this.toggleVisibility();
    this.toggleSearch();
    this.headerVisibility();
  }

  private toggleSearch(){
    const searchBtn = document.querySelector('.search-icon') as HTMLElement;
    const searchSubmit = document.querySelector('.search-submit') as HTMLElement;
    const searchClose = AccessibilityUtilities.convertAnchorToButton(
      document.querySelector('.search-close')
    );
    const searchModal = document.querySelector('.search-modal') as HTMLElement;
    const searchField = document.querySelector('.search-field') as HTMLElement;
    searchField.setAttribute("tabindex", "-1");
    searchSubmit.setAttribute("tabindex", "-1");
    searchClose.setAttribute("tabindex", "-1");

    searchBtn.addEventListener("click", (e) => {
      searchModal.style.width = "19.25rem";
      searchModal.style.padding = "0.625rem";
      searchField.removeAttribute("tabindex")
      searchSubmit.removeAttribute("tabindex");
      searchClose.removeAttribute("tabindex");
      e.preventDefault();
    });

    searchClose.addEventListener("click", (e) => {
      searchModal.style.width = "0";
      searchModal.style.padding = "0.625rem 0rem";
      searchField.setAttribute("tabindex", "-1");
      searchSubmit.setAttribute("tabindex", "-1");
      searchClose.setAttribute("tabindex", "-1");
      e.preventDefault();
    });
  }

  private handleResize() {
    const resize = () => {

      document.body.classList.add('menu_safe');

      let contentWidth = 0;
      Array.from(this.nav.querySelectorAll("ul li")).forEach((item:HTMLElement) => {
        contentWidth += item.offsetWidth;
      });
      let gap = this.nav.offsetWidth - contentWidth;
      let paddingLeft = parseInt(window.getComputedStyle(this.headertop).getPropertyValue('padding-left'));
      if (gap < paddingLeft + 120) {
        document.body.classList.remove('menu_safe');
      } else {
        document.body.classList.add('menu_safe');
      }

      return;

      if (
        !mobileMQ.matches &&
        !this.visible &&
        this.element.getAttribute("aria-hidden") === "true"
      ) {
        this.element.setAttribute("aria-hidden", "false");
      }
      if (
        mobileMQ.matches &&
        !this.visible &&
        this.element.getAttribute("aria-hidden") === "false"
      ) {
        this.element.setAttribute("aria-hidden", "true");
      }
    };
    window.addEventListener("resize", _debounce(resize, 100));
    resize();
  }

  private handleEsc() {
    window.addEventListener("keydown", (event) => {
      const key = event.key || event.keyCode;
      // Close the nav when the esc key is pressed while it's open
      if (key === "Escape" || key === "Esc" || key === 27) {
        if (this.visible) {
          this.visible = false;
          this.toggleVisibility();
          this.openButton.focus();
        }
      }
    });
  }

  protected handleTabbing() {
    window.addEventListener("keyup", (event) => {
      const key = event.key || event.keyCode;
      // Close the nav when tabbing outside of it while it's open
      if (key === "Tab" || key === 9) {
        if (
          this.visible &&
          !this.element.contains(event.target as HTMLElement)
        ) {
          this.visible = false;
          this.toggleVisibility();
        }
      }
    });
  }

  private handleOpenButtonClick() {
    let clickedViaKeyboard = false as boolean;
    // Set flag used in click handler to determine if click was handled via keyboard
    this.openButton.addEventListener("keydown", (event) => {
      if (
        event.key === " " ||
        event.key === "Enter" ||
        event.key === "Spacebar"
      ) {
        clickedViaKeyboard = true;
      }
    });

    this.openButton.addEventListener("click", () => {
      this.visible = !this.visible;
      this.toggleVisibility();
      if (clickedViaKeyboard && this.visible) {
        setTimeout(() => {
          this.focusableChildren[0]?.focus();
        }, 50);
      }
      // Reset state for subsequent click events
      clickedViaKeyboard = false;
    });
  }

  private handleTransitionEnd() {
    this.element.addEventListener("transitionend", (event) => {
      if (event.target === event.currentTarget) {
        if (this.element.classList.contains("transitioning")) {
          this.element.classList.remove("transitioning");
        }
      }
    });
  }

  public toggleVisibility() {
    if (true || mobileMQ.matches) {
      this.openButton.setAttribute("aria-expanded", `${this.visible}`);
      this.element.setAttribute("aria-hidden", `${!this.visible}`);
      this.element.classList.add("transitioning");
      const siteHeaderContainer = document.querySelector(".site-header");

      for (let i = 0; i < this.focusableChildren.length; i++) {
        this.focusableChildren[i].setAttribute(
          "tabindex",
          this.visible ? "0" : "-1"
        );
      }

      this.trigger(this.visible ? "show" : "hide");

      this.visible
        ? siteHeaderContainer.classList.add("site-header-open")
        : siteHeaderContainer.classList.remove("site-header-open");
    }
  }

  private headerVisibility(){
    const headerBG = document.querySelector(".site-header__top") as HTMLElement;
    let lastKnownScrollPosition: number = 0;
    let ticking: boolean = false;
    window.addEventListener("scroll", e => {
      lastKnownScrollPosition = window.scrollY;
      if (!ticking) {
        window.requestAnimationFrame(() => {
          animate2(lastKnownScrollPosition);
          ticking = false;
        });
        ticking = true;
      }
    }, true);

    function animate2(scrollPos: any) {
      if(document.querySelector("body").classList.contains("home-page")){
        if(scrollPos >= 120){
          if(!headerBG.classList.contains("scroll-bg")){
            headerBG.classList.add("scroll-bg");
          }
        }else if(scrollPos <= 150){
          headerBG.classList.remove("scroll-bg");
        }
      }
    }
  }

  public on(eventType: string, handler: Function) {
    if (this.eventHandlers.hasOwnProperty(eventType)) {
      this.eventHandlers[eventType].push(handler);
    } else {
      throw new Error(
        `Event type "${eventType}" is not allowed for MegaMenu component.`
      );
    }
  }

  private trigger(eventType: any) {
    if (this.eventHandlers.hasOwnProperty(eventType)) {
      for (let i = 0; i < this.eventHandlers[eventType].length; i++) {
        this.eventHandlers[eventType][i]();
      }
    } else {
      throw new Error(
        `Event type "${eventType}" is not allowed for MegaMenu component.`
      );
    }
  }
}

export default function siteHeaderInit() {
  const header = document.querySelector(
    ".site-header__mega-menu"
  ) as HTMLElement;
  new SiteHeader(header);
}
