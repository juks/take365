import React from 'react';
import ReactDOM from 'react-dom';
/*import Lightbox from './lightbox.jsx';*/
import Lightbox from 'react-images';

export default class Slideshow extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      lightboxIsOpen: true,
      images: [],
      currentImage: 0,
      date: props.date,
    };

    this.closeLightbox = this.closeLightbox.bind(this);
    this.gotoNext = this.gotoNext.bind(this);
    this.gotoPrevious = this.gotoPrevious.bind(this);
    this.handleClickImage = this.handleClickImage.bind(this);
    //this.openLightbox = this.openLightbox.bind(this);
  }

  componentDidMount() {
    this.load(this.state.date, 300);
  }

  load(date, span) {
    this.setState({
      isLoading: true,
    });

    const xhr = new XMLHttpRequest();

    xhr.open('GET', `/api/media/player-data?date=${date}&storyId=${window.pp.storyId}&span=${span}`);
    xhr.onload = () => {
      if (xhr.status !== 200) {
        notice('Ошибка при запросе данных', true);
        return;
      }
      const result = JSON.parse(xhr.response).result;
      const state = {
        currentImage: this.state.currentImage,
      };
      const media = result.media.map(m => {
        return {
          date: m.date,
          caption: m.title || m.description ? <div>
            {m.title ? <h1>{m.title}</h1> : null}
            <div dangerouslySetInnerHTML={{__html: m.description}}></div>
          </div> : null,
          src: m.thumb.url,
          srcset: [
            `${m.thumb.url} ${m.thumb.width}w`,
            `${m.thumbLarge.url} ${m.thumbLarge.width}w`,
          ],
        };
      });
      if (result.leftEdgeReached) {
        media[0].isFirst = true;
      }

      if (result.rightEdgeReached) {
        media[result.media.length - 1].isLast = true;
      }
      if (span < 0) {
        //if (!isFirstReq) {
        //  result.media.pop(); // TODO хак, убирает из фоток саму себя, которая нужна при первом запросе
        //}
        media.pop();
        state.images = [].concat(media, this.state.images);
        state.currentImage = this.state.currentImage + media.length;
      } else {
        //if (!isFirstReq) {
        //  result.media.shift(); // TODO хак, убирает из фоток саму себя, которая нужна при первом запросе
        //}
        state.images = this.state.images.concat(media);
      }

      if (!result.leftEdgeReached && state.currentImage === 0) {
        this.load(state.images[0].date, -300);
      }
      this.setState(state);
    };

    xhr.onerror = err => {
      notice('Ошибка при запросе', true);
    };

    xhr.send();
  }

  closeLightbox () {
    this.setState({
      lightboxIsOpen: false,
    });
  }

  gotoPrevious () {
    this.setState({
      currentImage: this.state.currentImage - 1,
    });
  }

  gotoNext () {
    this.setState({
      currentImage: this.state.currentImage + 1,
    });
  }

  handleClickImage () {
    if (this.state.currentImage === this.props.images.length - 1) return;

    this.gotoNext();
  }

  render() {
    return (
      <Lightbox
        images={this.state.images}
        isOpen={this.state.lightboxIsOpen}
        currentImage={this.state.currentImage}
        onClickPrev={this.gotoPrevious}
        onClickNext={this.gotoNext}
        onClose={this.closeLightbox}
        showImageCount={false}
        backdropClosesModal={true}
      />
    );
  }
}

window.slideshowRender = function(node, props) {
  ReactDOM.render(React.createElement(Slideshow, props), node);
};
