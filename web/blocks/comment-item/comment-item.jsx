import React from 'react';

function padLeft(num) {
  return ('0'+num).slice(-2);
}

export default class CommentItem extends React.Component {
  formatDate(timestamp) {
    const d = new Date(timestamp * 1000);

    return `${padLeft(d.getDate())}.${padLeft(d.getMonth() + 1)}.${d.getFullYear()} ${padLeft(d.getHours())}:${padLeft(d.getMinutes())}`;
  }

  remove() {
    var xhr = new XMLHttpRequest();
    var method = 'POST';
    var formData = new FormData(this.refs.form);
    formData.append('id', this.props.data.id);
    xhr.open(method, '/api/comment/delete-recover');
    xhr.send(formData);
    xhr.onload = () => {
      const data = JSON.parse(xhr.responseText);
      this.props.onRemoved(this.props.data.id);
    };
  }

  render() {
    const comment = this.props.data;

    const maxLevel = Math.min(comment.level, 5);
    const marginStyle = {
      marginLeft: maxLevel * 20,
    };

    if (comment.isDeleted) {
      return <div style={marginStyle} className={'comment' + (isAuthor ? ' comment-my' : '')} id={comment.id} data-debug={comment.thread + '-' + comment.level}>
        <i>комментарий удален</i>
      </div>;
    }

    const author = comment.author;
    const userpicStyle = author.userpic && author.userpic.url ? {
      backgroundImage: `url(${author.userpic.url})`,
    } : {};


    const isAuthor = this.props.user.id === author.id;

    return <div style={marginStyle} className={'comment' + (isAuthor ? ' comment-my' : '')} id={comment.id} data-debug={comment.thread + '-' + comment.level}>
      <div className="comment-header">
        <div className="comment-user fa fa-user">
          <a className="comment-user-img" href={author.url} style={userpicStyle}></a>
        </div>
        <div className="comment-username"><a href={author.url}>{author.username}</a></div>
        <time className="comment-date">{this.formatDate(comment.timestamp)}</time>
        <div className="comment-url"><a title="Ссылка на комментарий" href={`#${comment.id}`}>#</a></div>
        {isAuthor ?
          <div className="comment-trash"><a href="javascript:" onClick={this.remove.bind(this)} title="Удалить комментарий" className="fa fa-trash-o"></a></div>
        : null}
      </div>
      <div className="comment-text" dangerouslySetInnerHTML={{__html: comment.body}}></div>
      {this.props.children}
    </div>;
  }
}
