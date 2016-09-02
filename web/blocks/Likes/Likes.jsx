'use strict';

import React from 'react';
import ReactDOM from 'react-dom';

export default class FeedItem extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      isLiked: this.props.isLiked,
      likes: this.props.count,
    };
    this.onLikeToggle = this.onLikeToggle.bind(this);
  }

  onLikeToggle(e) {
    e.preventDefault();
    const xhr = new XMLHttpRequest();
    const like = !this.state.isLiked;
    const url = `/api/media/${this.props.id}/${like ? '' : 'un'}like`;
    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.responseType = 'json';
    this.setState({isLoading: true});
    xhr.onload = () => {
      const result = xhr.response.result;
      this.setState({
        isLoading: false,
        isLiked: like,
        likes: result,
      });
    };
    xhr.send();
  }

  render() {
    return <span className="feed-likes">
      <a href="#" className={`fa ${this.state.isLiked ? 'fa-heart' : 'fa-heart-o'} feed-like`} onClick={this.onLikeToggle}></a>
      {this.state.likes ? <sup className="feed-likes-total">{this.state.likes}</sup> : null}
    </span>;
  }
}
