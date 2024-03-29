'use strict';

import React from 'react';
import Likes from '../Likes/Likes.jsx';
import CommentList from '../CommentList/CommentList.jsx';

export default class FeedItem extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      showCommentsTextarea: false,
    };

    this.onShowCommentsTextarea = this.onShowCommentsTextarea.bind(this);
  }

  onShowCommentsTextarea(e) {
    e.preventDefault();
    this.setState({
      showCommentsTextarea: !this.state.showCommentsTextarea,
    });
  }

  render() {
    const data = this.props.data;
    return <div className="feed-item">
      <div className="feed-header">
        <time className="feed-date">{this.props.data.date}</time>
        { data.story.authors
          .map(author => {
            const userpicStyle = author.userpic && author.userpic.url ? {
              backgroundImage: `url(${author.userpic.url})`,
            } : {};
            return <div key={author.id} className="feed-user">
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
        <Likes id={this.props.data.id} isLiked={this.props.data.isLiked} count={this.props.data.likesCount} />
        <span className="feed-comments">
          <span className="feed-comments-count">
            <span className="fa fa-comments-o" aria-hidden="true"></span>
            <span className="feed-comments-total">{this.props.data.commentsCount}</span>
          </span>
          <a href="#" className="feed-comments-link" onClick={this.onShowCommentsTextarea}>Оставить комментарий</a>
        </span>
        <CommentList
          comments={this.props.data.comments}
          count={this.props.data.commentsCount}
          id={this.props.data.id}
          isMinimal={true}
          showTextarea={this.state.showCommentsTextarea}
          targetType="3"
          user={this.props.user}
        />
      </div>
    </div>;
  }
}
