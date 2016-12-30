import Autosuggest from 'react-autosuggest';
import React from 'react';
import ReactDOM from 'react-dom';

import './search.css';

export default class Search extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      suggestions: [],
      value: this.props.value || '',
    };

    this.onChange = this.onChange.bind(this);
    this.onClearRequested = this.onClearRequested.bind(this);
    this.onFetchRequested = this.onFetchRequested.bind(this);
    this.onSelected = this.onSelected.bind(this);
  }

  onChange(e, { newValue }) {
    this.setState({ value: newValue });
  }

  fetchSuggestion(filter) {
    return $.getJSON('/api/user/suggest/', {username: filter}).then(data => {
      return data.result;
    });
  }

  onClearRequested() {
    this.setState({
      suggestions: [],
    });
  }

  onFetchRequested({ value }) {
    this
      .fetchSuggestion(value)
      .then(suggestions => {
        this.setState({
          suggestions: suggestions,
        });
      });
  }

  onSelected(e, { suggestion }) {
    window.location.href = suggestion.url;
  }

  renderSuggestion(suggestion) {
    return <div>
      {suggestion.userpic &&
        <img
          src={suggestion.userpic.url}
          width={suggestion.userpic.width / 4}
          height={suggestion.userpic.height / 4}
        />
      }
      {' '}
      {suggestion.username}
    </div>;
  }

  getSuggestionValue(suggestion) {
    return suggestion.username;
  }

  render() {
    return <Autosuggest
      getSuggestionValue={this.getSuggestionValue}
      onSuggestionsClearRequested={this.onClearRequested}
      onSuggestionSelected={this.onSelected}
      onSuggestionsFetchRequested={this.onFetchRequested}
      renderSuggestion={this.renderSuggestion}
      suggestions={this.state.suggestions}
      inputProps={{
        value: this.state.value,
        type: 'search',
        onChange: this.onChange,
      }}
    />;
  }
}

window.searchRender = function(node, props) {
  ReactDOM.render(React.createElement(Search, props), node);
};
