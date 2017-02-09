'use strict';

import React, { PropTypes } from 'react';
import Comment from '../comment-item/comment-item.jsx';
import CommentForm from '../comment-form/comment-form.jsx';
import {TransitionMotion, spring} from 'react-motion';

export default class CommentList extends React.Component {
  constructor(props) {
    super(props);
    const comments = props.comments || [];

    this.state = {
      comments: this.filterDeleted(comments),
      isExpanded: false,
      isLoading: false,
    };
    this.nodes = [];

    this.loadMore = this.loadMore.bind(this);
  }

  filterDeleted(comments) {
    const filtered = [];
    let prev;
    for (let i = comments.length - 1; i >= 0; i--) {
      const c = comments[i];
      const hasChilds = prev && c.level === prev.level - 1;
      if (!c.isDeleted || hasChilds) {
        prev = c;
        filtered.unshift(c);
      }
    }
    return filtered;
  }

  onNew(newComment, parentId) {
    const parentIndex = this.state.comments.findIndex(c => c.id === parentId);
    let i;
    for (i = parentIndex + 1; i < this.state.comments.length; i++) {
      const c = this.state.comments[i];
      if (newComment.level > c.level) {
        break;
      }
    }

    this.state.comments.splice(i, 0, newComment);
    this.setState({comments: this.state.comments});
  }

  onRemoved(id) {
    const comments = this.state.comments.filter(c => c.id !== id);
    this.setState({comments: comments});
  }

  loadMore() {
    const xhr = new XMLHttpRequest();
    const url = `/api/media/${this.props.id}/comments`;
    xhr.open('GET', url);
    xhr.responseType = 'json';
    this.setState({isLoading: true});
    xhr.onload = () => {
      const result = xhr.response.result;
      this.setState({
        isLoading: false,
        isExpanded: true,
        comments: this.filterDeleted(result),
      });
    };
    xhr.send();
  }

  getStyles() {
    return this.state.comments.map(c => {
      return {
        key: '' + c.id,
        data: c,
        style: {
          opacity: spring(1),
        },
      };
    });
  }

  willEnter() {
    return {
      opacity: 0,
      height: 0,
    };
  }

  willLeave() {
    return {
      opacity: spring(0),
    };
  }

  render() {
    return <div className="comments-inner">
        {!this.props.isMinimal ?
          <h2 className="comments-title">
            { this.state.comments.length ?
              `Комментарии (${this.state.comments.length})`
            : 'Нет комментариев'}
          </h2>
        : null }
        <div className="comments-footer">
          <CommentForm
            id={this.props.id}
            isMinimal={this.props.isMinimal}
            onNew={this.onNew.bind(this)}
            targetType={this.props.targetType}
            user={this.props.user}
          />
        </div>
        { this.state.comments ?
          <TransitionMotion willLeave={this.willLeave.bind(this)} willEnter={this.willEnter} styles={this.getStyles()}>
            { styles =>
              // empty div for remove warning onlyChild
              <div className="comments-list">
              { styles.map(config => {
                return <div key={config.key} style={config.style} ref={el => this.nodes[config.key] = el}>
                  <Comment data={config.data} user={this.props.user} onRemoved={this.onRemoved.bind(this)}>{
                    (() => {
                      if (!this.props.user) {
                        return null;
                      } else if (this.state.replyOpen !== config.data.id) {
                        return <div className="comment-options">
                          <a className="comment-options-item" href="javascript:" onClick={()=>this.setState({replyOpen: config.data.id})}>Ответить</a>
                        </div>;
                      } else {
                        return <CommentForm
                          id={this.props.id}
                          parentId={config.data.id}
                          targetType={this.props.targetType}
                          user={this.props.user}
                          onNew={(...args) => {
                            this.onNew(...args);
                            this.setState({replyOpen: null});
                          }}
                        >
                          <a className="cancel" href="javascript:" onClick={()=>this.setState({replyOpen: null})}>Отмена</a>
                        </CommentForm>;
                      }
                    })()
                  }</Comment>
                </div>;
              })}
              </div>
            }
          </TransitionMotion>
        : null}
        {this.props.isMinimal && this.props.count && !this.state.isExpanded ?
          <div className="comments-load">
            <button className="btn" onClick={this.loadMore} disabled={this.state.isLoading}>Загрузить все</button>
          </div>
        : null }
      </div>;
  }
}

CommentList.propTypes = {
  count: PropTypes.number,
  commentsCount: PropTypes.array,
  user: PropTypes.object,
  id: PropTypes.number,
  isMinimal: PropTypes.bool,
  targetType: PropTypes.string,
};

CommentList.defaultProps = {
  targetType: '2',
};
