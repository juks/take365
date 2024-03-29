// fork https://github.com/jossmac/react-images

import React, { Component, PropTypes } from 'react';
import Swipeable from 'react-swipeable';
import utils from './utils';
import Fade from './Fade';
import Icon from './Icon.jsx';
import Likes from '../Likes/Likes.jsx';
import Portal from 'react-portal';
import classes from './styles/default.css';

export default class Lightbox extends Component {
  constructor() {
    super();

    utils.bindFunctions.call(this, [
      'onCloseFade',
      'gotoNext',
      'gotoPrev',
      'handleImageClick',
      'handleKeyboardInput',
      'handleResize',
    ]);

    this.state = { windowHeight: 0 };
  }

  componentWillMount() {
    utils.bodyScroll.blockScroll();
    if (this.props.enableKeyboardInput) {
      window.addEventListener('keydown', this.handleKeyboardInput);
      window.addEventListener('resize', this.handleResize);
      this.handleResize();
    }
  }

  componentWillUnmount() {
    utils.bodyScroll.allowScroll();
    window.removeEventListener('keydown', this.handleKeyboardInput);
    window.removeEventListener('resize', this.handleResize);
  }

  // ==============================
  // METHODS
  // ==============================

  onCloseFade (e) {
    if (e.currentTarget !== e.target) {
      return;
    }

    if (this.props.backdropClosesModal && this.props.onClose) {
      this.props.onClose();
    }
  }
  gotoNext (event) {
    if (this.props.currentImage === (this.props.images.length - 1)) return;
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    this.props.onClickNext();

  }
  gotoPrev (event) {
    if (this.props.currentImage === 0) return;
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    this.props.onClickPrev();

  }
  handleImageClick() {
    if (!this.props.onClickImage) return;

    this.props.onClickImage();

  }
  handleKeyboardInput (event) {
    if (event.keyCode === 37) {
      this.gotoPrev(event);
      return true;
    } else if (event.keyCode === 39) {
      this.gotoNext(event);
      return true;
    } else if (event.keyCode === 27) {
      this.props.onClose();
      return true;
    }
    return false;

  }
  handleResize() {
    this.setState({
      windowHeight: window.innerHeight || 0,
    });

  }

  // ==============================
  // RENDERERS
  // ==============================

  renderArrowPrev() {
    if (this.props.currentImage === 0) return null;

    return (
      <button title="Previous (Left arrow key)"
        type="button"
        className={`${classes.arrow} ${classes.arrowPrev}`}
        onClick={this.gotoPrev}
        onTouchEnd={this.gotoPrev}
      >
        <Icon type="arrowLeft" />
      </button>
    );
  }
  renderArrowNext() {
    if (this.props.currentImage === (this.props.images.length - 1)) return null;
    return (
      <button title="Next (Right arrow key)"
        type="button"
        className={`${classes.arrow} ${classes.arrowNext}`}
        onClick={this.gotoNext}
        onTouchEnd={this.gotoNext}
        >
        <Icon type="arrowRight" />
      </button>
    );
  }
  renderCloseButton() {
    if (!this.props.showCloseButton) return null;

    return (
      <div className={classes.closeBar}>
        <button
          title="Close (Esc)"
          className={classes.closeButton}
          onClick={this.props.onClose}
          >
          <Icon type="close" />
        </button>
      </div>
    );
  }
  renderDialog() {
    if (!this.props.isOpen) return null;

    return <Fade id="react-images-container"
      duration={250}
      className={classes.container}
      onClick={this.onCloseFade}
      onTouchEnd={this.onCloseFade}
    >
      <span className={classes.contentHeightShim} />
      <div className={classes.content}>
        {this.renderCloseButton()}
        {this.renderImages()}
      </div>
      {this.renderArrowPrev()}
      {this.renderArrowNext()}
    </Fade>;
  }
  renderFooter(image) {
    const figcaption = image.caption
      ? <figcaption className={classes.footerCaption}>{image.caption}</figcaption>
      : null;

    return <div className={classes.footer}>
      {figcaption}
      <Likes id={image.id} isLiked={image.isLiked} count={image.likesCount} />
    </div>;
  }
  renderImages() {
    const { images, currentImage } = this.props;
    const { windowHeight } = this.state;

    if (!images || !images.length) return null;

    const image = images[currentImage];

    let srcset;
    let sizes;

    if (image.srcset) {
      srcset = image.srcset.join();
      sizes = '100vw';
    }

    return (
      <figure key={`image ${currentImage}`}
        className={classes.figure}
        style={{ maxWidth: this.props.width }}
        >
        <Swipeable onSwipedLeft={this.gotoNext} onSwipedRight={this.gotoPrev}>
          <img className={classes.image}
            onClick={this.handleImageClick}
            sizes={sizes}
            src={image.src}
            srcSet={srcset}
            style={{
              cursor: this.props.onClickImage ? 'pointer' : 'auto',
              maxHeight: windowHeight,
            }}
          />
        </Swipeable>
        {this.renderFooter(image)}
      </figure>
    );
  }
  render() {
    return <Portal isOpened={true}>
      {this.renderDialog()}
    </Portal>;
  }
}

Lightbox.displayName = 'Lightbox';

Lightbox.propTypes = {
  backdropClosesModal: PropTypes.bool,
  currentImage: PropTypes.number,
  enableKeyboardInput: PropTypes.bool,
  images: PropTypes.arrayOf(
    PropTypes.shape({
      src: PropTypes.string.isRequired,
      srcset: PropTypes.array,
      caption: PropTypes.string,
    })
  ).isRequired,
  isOpen: PropTypes.bool,
  onClickImage: PropTypes.func,
  onClickNext: PropTypes.func,
  onClickPrev: PropTypes.func,
  onClose: PropTypes.func.isRequired,
  showCloseButton: PropTypes.bool,
  width: PropTypes.number,
};

Lightbox.defaultProps = {
  currentImage: 0,
  enableKeyboardInput: true,
  onClickShowNextImage: true,
  showCloseButton: true,
  width: 900,
};
