'use strict';

import React from 'react';
import ReactDOM from 'react-dom';

export default class FeedItem extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      isLiked: this.props.data.isLiked,
      likes: this.props.data.likesCount,
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
      const result = JSON.parse(xhr.response).result;
      this.setState({
        isLoading: false,
        isLiked: like,
        likes: result,
      });
    };
    xhr.send();
  }

  render() {
    const data = this.props.data;
    return <div className="feed-item">
      <div className="feed-header">
        <time className="feed-date" pubdate={this.props.data.date}>{this.props.data.date}</time>
        { data.story.authors
          .map(author => {
            const userpicStyle = author.userpic && author.userpic.url ? {
              backgroundImage: `url(${author.userpic.url})`,
            } : {};
            return <div className="feed-user">
              <div className="feed-userpic fa fa-user">
                <a href={author.url} className="feed-userpic-img" style={userpicStyle}></a>
              </div>
              <div className="feed-username">
                <a href={author.url} key={author.username}>{author.username}</a>
              </div>
            </div>;
          })
        }
        <span className="fa fa-long-arrow-right sep"></span><a href={data.story.url}>{data.story.title}</a>
      </div>
      <div className="feed-content">
        <div className="feed-img">
          <img src={data.thumb.url} width={data.thumb.width} height={data.thumb.height} srcSet={`${data.thumbLarge.url} 2x`} />
        </div>
        <div className="feed-desc">
          { data.title ?
            <p>{data.title}</p>
          : null }
          { data.description ?
            <p dangerouslySetInnerHTML={{__html: data.description}}></p>
          : null }
        </div>
      </div>
      <div className="feed-footer">
        <span className="feed-likes">
          <a href="#" className={`fa ${this.state.isLiked ? 'fa-heart' : 'fa-heart-o'} feed-like`} onClick={this.onLikeToggle}></a>
          {this.state.likes ? <sup className="feed-likes-total">{this.state.likes}</sup> : null}
        </span>
      </div>
    </div>;
  }
}
