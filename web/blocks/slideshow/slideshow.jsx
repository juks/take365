import React from 'react';
import ReactDOM from 'react-dom';
import Lightbox from './Lightbox.jsx';
import { Router, Route, browserHistory } from 'react-router';

export default class Slideshow extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      lightboxIsOpen: true,
      images: [],
      currentImage: 0,
    };

    this.closeLightbox = this.closeLightbox.bind(this);
    this.gotoNext = this.gotoNext.bind(this);
    this.gotoPrevious = this.gotoPrevious.bind(this);
    this.handleClickImage = this.handleClickImage.bind(this);
  }

  componentDidMount() {
    this.load(this.props.routeParams.photoDate, 300);
  }

  getIndexByDate(date) {
    //this.state.images.findIndex(m => m.date === date);
    for (let i = 0; i < this.state.images.length; i++) {
      if (this.state.images[i].date === date) {
        return i;
      }
    }
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
      };
      // hack, need url from json
      const routeParams = this.props.routeParams;
      const media = result.media.map(m => {
        return {
          id: m.id,
          isLiked: m.isLiked,
          likesCount: m.likesCount,
          url: `/${routeParams.username}/story/${routeParams.storyId}/${m.date}`,
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
      } else {
        //if (!isFirstReq) {
        //  result.media.shift(); // TODO хак, убирает из фоток саму себя, которая нужна при первом запросе
        //}
        state.images = this.state.images.concat(media);
      }

      if (!result.leftEdgeReached) {
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
    const routeParams = this.props.routeParams;
    browserHistory.push(`/${routeParams.username}/story/${routeParams.storyId}`);
    this.setState({
      lightboxIsOpen: false,
    });
  }

  gotoPrevious () {
    const newIndex = this.getIndexByDate(this.props.routeParams.photoDate) - 1;
    browserHistory.push(this.state.images[newIndex].url);
  }

  gotoNext () {
    const newIndex = this.getIndexByDate(this.props.routeParams.photoDate) + 1;
    browserHistory.push(this.state.images[newIndex].url);
  }

  handleClickImage () {
    if (this.props.routeParams.photoDate === this.state.images[this.state.images.length - 1]) return;

    this.gotoNext();
  }

  render() {
    return (
      <Lightbox
        images={this.state.images}
        isOpen={this.state.lightboxIsOpen}
        currentImage={this.getIndexByDate(this.props.routeParams.photoDate)}
        onClickPrev={this.gotoPrevious}
        onClickNext={this.gotoNext}
        onClose={this.closeLightbox}
        backdropClosesModal={true}
      />
    );
  }
}

class Story extends React.Component {
  render() {
    return <div />;
  }
}

window.slideshowRender = function(node, props) {
  ReactDOM.render(
    <Router history={browserHistory}>
      <Route path="/:username/story/:storyId" component={Story} />
      <Route path="/:username/story/:storyId/:photoDate" component={Slideshow}/>
    </Router>
  , node);
};

window.slideshowHistoryPush = function(url) {
  browserHistory.push(url);
};
