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

  render() {
    const data = this.props.data;
    return <div className="feed-item">
      <div>
        { data.story.authors
          .map(author => <a href={author.url} key={author.username}>{author.username}</a>)
        }
        &nbsp;→ <a href={data.story.url}>{data.story.title}</a>
      </div>
      <div>
        <a href="">{this.state.isLiked ? '♥' : '♡'}</a>
        <a href={`${data.story.url}#${data.date}`}>
          <img src={data.thumb.url} width={data.thumb.width} height={data.thumb.height} srcSet={`${data.thumbLarge.url} 2x`} />
        </a>
      </div>
      { data.title ?
        <p>{data.title}</p>
      : null }
      { data.description ?
        <p dangerouslySetInnerHTML={{__html: data.description}}></p>
      : null }
    </div>;
  }
}
