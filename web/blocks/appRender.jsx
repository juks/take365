import CommentList from './CommentList/CommentList.jsx';
import Feed from './_Feed/_Feed.jsx';
import Follow from './follow/follow.jsx';
import React from 'react';
import ReactDOM from 'react-dom';

import TEMP3 from './slideshow/slideshow.jsx';

import './Search/Search.jsx';

window.appRender = function(node, props) {
  ReactDOM.render(React.createElement(CommentList, props), node);
};

window.feedRender = function(node, props) {
  ReactDOM.render(React.createElement(Feed, props), node);
};

window.followRender = function(node, props) {
  ReactDOM.render(React.createElement(Follow, props), node);
};
