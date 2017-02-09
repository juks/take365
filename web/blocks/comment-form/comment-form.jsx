'use strict';

import React from 'react';

export default class CommentForm extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isSending: false,
      value: '',
    };

    this.onSubmit = this.onSubmit.bind(this);
  }

  componentDidMount() {
    if (this.props.parentId) {
      this.refs.body.focus();
    }
  }

  onSubmit(e) {
    if (e) {
      e.preventDefault();
    }
    const xhr = new XMLHttpRequest();
    xhr.responseType = 'json';

    const method = 'POST';//isCreate ? 'POST' : 'PUT';
    xhr.open(method, '/api/comment/write');

    const formData = new FormData(this.refs.form);
    formData.append('targetId', this.props.id);
    if (this.props.parentId) {
      formData.append('parentId', this.props.parentId);
    }
    formData.append('targetType', this.props.targetType);

    xhr.send(formData);
    this.setState({isSending: true});
    xhr.onload = () => {
      this.refs.form.body.value = '';
      const data = xhr.response;
      this.setState({isSending: false});
      this.props.onNew(data.result, this.props.parentId);
    };
  }

  onChange(e) {
    this.setState({error: false});
    this.setState({value: e.target.value});
  }

  onKeyDown(e) {
    if ((e.keyCode === 10 || e.keyCode === 13) && e.ctrlKey) {
      e.currentTarget.blur();
      this.onSubmit();
    }
  }

  render() {
    if (!this.props.user && !this.props.isMinimal) {
      return <div className="comments-info">
        <span className="fa fa-exclamation-triangle"></span>
        Только зарегистрированные пользователи могут участвовать в обсуждениях.<br />
        Если вы хотите оставить комментарий, пожалуйста, <a href="#" onClick={Auth.open}>представьтесь</a> или <a href="/#2">зарегистрируйтесь</a>.
      </div>;
    }
    return <form className="form form-comment" ref="form" onSubmit={this.onSubmit}>
      <fieldset className={this.state.error ? 'error' : ''}>
        <textarea ref="body" onKeyDown={this.onKeyDown.bind(this)} onChange={this.onChange.bind(this)} rows="10" cols="30" name="body"></textarea>
        <span className="error-message"></span>
      </fieldset>
      <fieldset>
        <input type="submit" value="Отправить" disabled={!this.state.value || this.state.isSending} />
        {this.props.children}
      </fieldset>
    </form>;
  }
}
