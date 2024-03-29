'use strict';

import React from 'react';
import FeedItem from '../feed-item/feed-item.jsx';

export default class Feed extends React.Component {
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
    xhr.responseType = 'json';
    const data = this.state.data;

    let params = 'lastComments=3';
    if (data.length) {
      params = `&firstTime=${data[data.length - 1].timestamp}`;
    }
    xhr.open('GET', '/api/feed/feed?' + params);
    xhr.onload = () => {
      const data = xhr.response;
      this.setState({
        data: this.state.data.concat(data.result.list),
        isEmpty: data.result.isEmpty,
        isLoading: false,
      });
    };
    xhr.send();
  }

  render() {
    return <div className="feed-wrapper">
      { (() => {
        return <div className="feed-inner">
          <div className="feed-list">
            { this.state.data.map(item => <FeedItem
                data={item}
                user={this.props.user}
                key={item.id}
              ></FeedItem>) }
          </div>
          <div className="feed-load">
            { (() => {
              if (this.state.isLoading) {
               return <p>Загружается</p>;
              } else if (this.state.isEmpty) {
                return <p>Пока фотографий нет</p>;
              } else {
                return <div className="feed-load-more"><button className="btn" onClick={this.load}>Загрузить ещё</button></div>;
              }
            })()}
          </div>
        </div>;
      })()}
    </div>;
  }
}
