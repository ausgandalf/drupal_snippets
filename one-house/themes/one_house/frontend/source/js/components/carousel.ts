import Flickity from "flickity";
import "flickity-imagesloaded";
import ScrollMagic from 'scrollmagic';

export class Carousel {
  private element: HTMLElement;
  private elementNavNext: HTMLElement;
  private elementNavPrevious: HTMLElement;
  private elementImage: NodeListOf<HTMLElement>;
  private carousel: Flickity;

  constructor(element: HTMLElement) {
    if (!!element && !!element.querySelector(".carousel")) {
      this.element = element;
      this.elementNavNext = this.element.querySelector(
        ".carousel__nav-next"
      ) as HTMLElement;
      this.elementNavPrevious = this.element.querySelector(
        ".carousel__nav-previous"
      ) as HTMLElement;
      this.elementImage = document.querySelectorAll(
        ".carousel__slide-image"
      ) as NodeListOf<HTMLElement>;
      this.carousel = new Flickity(this.element.querySelector(".carousel"), {
        contain: true,
        imagesLoaded: true,
        wrapAround: true,
        lazyLoad: 2,
        pageDots: true,
        prevNextButtons: false,
        adaptiveHeight: true,
      });
      this.addCarouselEventlisteners();
    }
    const controller = new ScrollMagic.Controller();

    const scene = new ScrollMagic.Scene({
      triggerElement: '.carousel',
    })
    .setClassToggle('.carousel__slide-image','ready')
    .addTo(controller);

    const scene2 = new ScrollMagic.Scene({
      triggerElement: '.carousel',
    })
    .setClassToggle('.carousel__nav-previous','disabled')
    .addTo(controller);

    const scene3 = new ScrollMagic.Scene({
      triggerElement: '.carousel',
    })
    .setClassToggle('.carousel__nav-next','ready')
    .addTo(controller);
  }
  private addCarouselEventlisteners() {
    // onClick
    this.elementNavNext.addEventListener("click", (event) => {
      this.carousel.next();
      this.elementNavPrevious.classList.remove('disabled');
      if(!this.elementNavPrevious.classList.contains('ready')){
        this.elementNavPrevious.classList.add('ready');
      }
    });

    this.elementNavPrevious.addEventListener("click", (event) => {
      this.carousel.previous();
      this.elementNavPrevious.classList.remove('disabled');
      if(!this.elementNavPrevious.classList.contains('ready')){
        this.elementNavPrevious.classList.add('ready');
      }
    });
  }
}

export default function carouselsInit() {
  const carousels = document.querySelectorAll(
    ".carousel-holder"
  ) as NodeListOf<HTMLElement>;
  for (let i = 0; i < carousels.length; i++) {
    new Carousel(carousels[i]);
  }
}
