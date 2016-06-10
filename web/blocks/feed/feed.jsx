'use strict';

import React from 'react';
import ReactDOM from 'react-dom';
import FeedItem from '../feed-item/feed-item.jsx';

class Feed extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      data: [],
      isEmpty: false,
      isLoading: true,
    };

    this.load = this.load.bind(this);
  }

  componentDidMount() {
    this.load();
  }

  load() {
    this.setState({
      isLoading: true,
    });
    const xhr = new XMLHttpRequest();
    let params = '';
    const data = this.state.data;
    if (data.length) {
      params = `firstTime=${data[data.length - 1].timestamp}`;
    }
    xhr.open('GET', '/api/feed/feed?' + params);
    xhr.onload = () => {
      const data = JSON.parse(xhr.responseText);
      this.setState({
        data: this.state.data.concat(data.result.list),
        isEmpty: data.result.isEmpty,
        isLoading: false,
      });
    };
    xhr.send();
  }

  render() {
    return <div>
      { (() => {
        return <div>
          <div>
            { this.state.data.map(item => <FeedItem data={item} key={item.id}></FeedItem>) }
          </div>
          { (() => {
            if (this.state.isLoading) {
             return <div>Загружается</div>;
            } else if (this.state.isEmpty) {
              return <div>Пока фотографий нет</div>;
            } else {
              return <p><button onClick={this.load}>Загрузить еще</button></p>;
            }
          })()}
        </div>;
      })()}
    </div>;
  }
}

window.feedRender = function(node, props) {
  ReactDOM.render(React.createElement(Feed, props), node);
};
