'use strict';

import React from 'react';
import ReactDOM from 'react-dom';
import Comment from '../comment-item/comment-item.jsx';
import CommentForm from '../comment-form/comment-form.jsx';
import {TransitionMotion, spring} from 'react-motion';

import TEMP1 from '../follow/follow.jsx';
import TEMP2 from '../feed/feed.jsx';
import TEMP3 from '../slideshow/slideshow.jsx';

class CommentList extends React.Component {
  constructor(props) {
    super(props);
    const comments = props.comments || [];

    this.state = {
      comments: this.filterDeleted(comments),
    };
    this.nodes = [];
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
    return <div>
        <h2 className="comments-title">
          { this.state.comments.length ?
            `Комментарии (${this.state.comments.length})`
          : 'Нет комментариев'}
        </h2>
        <div className="comments-footer">
          <CommentForm user={this.props.user} contentId={this.props.id} onNew={this.onNew.bind(this)} />
        </div>
        { this.state.comments ?
          <TransitionMotion willLeave={this.willLeave.bind(this)} willEnter={this.willEnter} styles={this.getStyles()}>
            { styles =>
              // empty div for remove warning onlyChild
              <div>
              { styles.map(config => {
                return <div key={config.key} style={config.style} ref={el => this.nodes[config.key] = el}>
                  <Comment data={config.data} user={this.props.user} onRemoved={this.onRemoved.bind(this)}>{
                    (() => {
                      if (!this.props.user.id) {
                        return null;
                      } else if (this.state.replyOpen !== config.data.id) {
                        return <div className="comment-options">
                          <a className="comment-options-item" href="javascript:" onClick={()=>this.setState({replyOpen: config.data.id})}>Ответить</a>
                        </div>;
                      } else {
                        return <CommentForm user={this.props.user} parentId={config.data.id} contentId={this.props.id} onNew={(...args) => {
                          this.onNew(...args);
                          this.setState({replyOpen: null});
                        }}>
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
      </div>;
  }
}

window.appRender = function(node, props) {
  ReactDOM.render(React.createElement(CommentList, props), node);
};
