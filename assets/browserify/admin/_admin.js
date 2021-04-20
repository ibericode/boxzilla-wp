const $ = window.jQuery
const Option = require('./_option.js')
const optionControls = document.getElementById('boxzilla-box-options-controls')
const $optionControls = $(optionControls)
const tnLoggedIn = document.createTextNode(' logged in')
const EventEmitter = require('wolfy87-eventemitter')
const events = new EventEmitter()
const Designer = require('./_designer.js')($, Option, events)
const rowTemplate = window.wp.template('rule-row-template')
const i18n = window.boxzilla_i18n
const ruleComparisonEl = document.getElementById('boxzilla-rule-comparison')
const rulesContainerEl = document.getElementById('boxzilla-box-rules')
const ajaxurl = window.ajaxurl

// events
$(window).on('load', function () {
  if (typeof (window.tinyMCE) === 'undefined') {
    document.getElementById('notice-notinymce').style.display = ''
  }

  $optionControls.on('click', '.boxzilla-add-rule', addRuleFields)
  $optionControls.on('click', '.boxzilla-remove-rule', removeRule)
  $optionControls.on('change', '.boxzilla-rule-condition', setContextualHelpers)
  $optionControls.find('.boxzilla-auto-show-trigger').on('change', toggleTriggerOptions)
  $(ruleComparisonEl).change(toggleAndOrTexts)
  $('.boxzilla-rule-row').each(setContextualHelpers)
})

function toggleAndOrTexts () {
  var newText = ruleComparisonEl.value === 'any' ? i18n.or : i18n.and
  $('.boxzilla-andor').text(newText)
}

function toggleTriggerOptions () {
  $optionControls.find('.boxzilla-trigger-options').toggle(this.value !== '')
}

function removeRule () {
  var row = $(this).parents('tr')

  // delete andor row
  row.prev().remove()

  // delete rule row
  row.remove()
}

function setContextualHelpers () {
  var context = (this.tagName.toLowerCase() === 'tr') ? this : $(this).parents('tr').get(0)
  var condition = context.querySelector('.boxzilla-rule-condition').value
  var valueInput = context.querySelector('.boxzilla-rule-value')
  var qualifierInput = context.querySelector('.boxzilla-rule-qualifier')
  var betterInput = valueInput.cloneNode(true)
  var $betterInput = $(betterInput)

  // remove previously added helpers
  $(context.querySelectorAll('.boxzilla-helper')).remove()

  // prepare better input
  betterInput.removeAttribute('name')
  betterInput.className = betterInput.className + ' boxzilla-helper'
  valueInput.parentNode.insertBefore(betterInput, valueInput.nextSibling)
  $betterInput.change(function () {
    valueInput.value = this.value
  })

  betterInput.style.display = ''
  valueInput.style.display = 'none'
  qualifierInput.style.display = ''
  qualifierInput.querySelector('option[value="not_contains"]').style.display = 'none'
  qualifierInput.querySelector('option[value="contains"]').style.display = 'none'
  if (tnLoggedIn.parentNode) {
    tnLoggedIn.parentNode.removeChild(tnLoggedIn)
  }

  // change placeholder for textual help
  switch (condition) {
    default:
      betterInput.placeholder = i18n.enterCommaSeparatedValues
      break

    case '':
    case 'everywhere':
      qualifierInput.value = '1'
      valueInput.value = ''
      betterInput.style.display = 'none'
      qualifierInput.style.display = 'none'
      break

    case 'is_single':
    case 'is_post':
      betterInput.placeholder = i18n.enterCommaSeparatedPosts
      $betterInput.suggest(ajaxurl + '?action=boxzilla_autocomplete&type=post', {
        multiple: true,
        multipleSep: ','
      })
      break

    case 'is_page':
      betterInput.placeholder = i18n.enterCommaSeparatedPages
      $betterInput.suggest(ajaxurl + '?action=boxzilla_autocomplete&type=page', {
        multiple: true,
        multipleSep: ','
      })
      break

    case 'is_post_type':
      betterInput.placeholder = i18n.enterCommaSeparatedPostTypes
      $betterInput.suggest(ajaxurl + '?action=boxzilla_autocomplete&type=post_type', {
        multiple: true,
        multipleSep: ','
      })
      break

    case 'is_url':
      qualifierInput.querySelector('option[value="contains"]').style.display = ''
      qualifierInput.querySelector('option[value="not_contains"]').style.display = ''
      betterInput.placeholder = i18n.enterCommaSeparatedRelativeUrls
      break

    case 'is_post_in_category':
      $betterInput.suggest(ajaxurl + '?action=boxzilla_autocomplete&type=category', {
        multiple: true,
        multipleSep: ','
      })
      break

    case 'is_post_with_tag':
      $betterInput.suggest(ajaxurl + '?action=boxzilla_autocomplete&type=post_tag', {
        multiple: true,
        multipleSep: ','
      })
      break

    case 'is_user_logged_in':
      betterInput.style.display = 'none'
      valueInput.parentNode.insertBefore(tnLoggedIn, valueInput.nextSibling)
      break

    case 'is_referer':
      qualifierInput.querySelector('option[value="contains"]').style.display = ''
      qualifierInput.querySelector('option[value="not_contains"]').style.display = ''
      break
  }
}

function addRuleFields () {
  var data = {
    key: optionControls.querySelectorAll('.boxzilla-rule-row').length,
    andor: ruleComparisonEl.value === 'any' ? i18n.or : i18n.and
  }
  var html = rowTemplate(data)
  $(rulesContainerEl).append(html)
  return false
}

module.exports = {
  Designer: Designer,
  Option: Option,
  events: events
}
