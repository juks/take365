'use strict';

import React from 'react';
import ReactDOM from 'react-dom';

class CommentList extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      isLoading: false,
      isFollowing: props.isFollowing,
    };
    this.onToggle = this.onToggle.bind(this);
  }

  onToggle(e) {
    e.preventDefault();
    const xhr = new XMLHttpRequest();
    const method = 'POST';//isCreate ? 'POST' : 'PUT';
    const follow = !this.state.isFollowing;
    const url = follow ? '/api/feed/follow' : '/api/feed/unfollow';
    xhr.open(method, url);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(`username=@${this.props.storyUserId}`);
    this.setState({isLoading: true});
    xhr.onload = () => {
      this.setState({
        isLoading: false,
        isFollowing: follow,
      });
    };
  }

  render() {
    return <a href="#" className={`btn btn-${this.state.isFollowing ? 'red' : 'green'}`} onClick={this.onToggle}>{
      this.state.isFollowing ? 'Отписаться' : 'Подписаться'
    }</a>;
  }
}

window.followRender = function(node, props) {
  ReactDOM.render(React.createElement(CommentList, props), node);
};
