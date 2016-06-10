'use strict';

import React from 'react';
import ReactDOM from 'react-dom';

export default class FeedItem extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      isLiked: false,
    };
    this.onLikeToggle = this.onLikeToggle.bind(this);
  }

  onLikeToggle(e) {
    e.preventDefault();
    const xhr = new XMLHttpRequest();
    const like = !this.state.isLiked;
    const url = `/api/media/${this.props.data.id}/${like ? '' : 'un'}like`;
    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    this.setState({isLoading: true});
    xhr.onload = () => {
      this.setState({
        isLoading: false,
        isLiked: like,
      });
    };
    xhr.send();
  }

  render() {
    const data = this.props.data;
    return <div className="feed-item">
      <div className="feed-item-header">
        { data.story.authors
          .map(author => <a href={author.url} key={author.username}>{author.username}</a>)
        }
        &nbsp;→ <a href={data.story.url}>{data.story.title}</a>
      </div>
      <div className="feed-item-content">
        <div className="feed-item-img">
          <a href={`${data.story.url}#${data.date}`}>
            <img src={data.thumb.url} width={data.thumb.width} height={data.thumb.height} srcSet={`${data.thumbLarge.url} 2x`} />
          </a>
        </div>
        <div className="feed-item-desc">
          { data.title ?
            <p>{data.title}</p>
          : null }
          { data.description ?
            <p dangerouslySetInnerHTML={{__html: data.description}}></p>
          : null }
        </div>
      </div>
      <div className="feed-item-footer">
        <a href="" onClick={this.onLikeToggle}>{this.state.isLiked ? '♥' : '♡'}</a>
      </div>
    </div>;
  }
}
