/*!
 * paginga - jQuery Pagination Plugin v0.8.1
 * https://github.com/mrk-j/paginga
 *
 * Copyright 2017 Mark and other contributors
 * Released under the MIT license
 * https://github.com/mrk-j/paginga/blob/master/LICENSE
 */
;(function ($, window, document, undefined)
{
    "use strict";

        var pluginName = "paginga",
            defaults = {
                itemsPerPage: 5,
                itemsContainer: ".items",
                item: "> .item",
                page: 1,
                nextPage: ".nextPage",
                previousPage: ".previousPage", 
                firstPage: ".firstPage",
                lastPage: ".lastPage",
                pageNumbers: ".pageNumbers",
                maxPageNumbers: false,
                currentPageClass: "active",
                pager: ".pager",
                autoHidePager: true,
                scrollToTop: {
                    offset: 15,
                    speed: 100,
                },
                history: false,
                historyHashPrefix: "page-"
            };

        // The actual plugin constructor
        function paginga(element, options)
        {
            this.element = element;
            this.settings = $.extend( true, {}, defaults, options );
            this._defaults = defaults;
            this._name = pluginName;
            this._ready = false;
            this.currentPage = this.settings.page;
            this.items = $(this.element).find(this.settings.itemsContainer + " " + this.settings.item);
            this.totalPages = Math.ceil(this.items.length / this.settings.itemsPerPage);

            if(this.totalPages <= 1)
            {
                $('.submitBtn').removeClass('hidden');
                $('.nextPage').addClass('hidden');
                $('.total-page').text(this.totalPages);
                $(this.element).find(this.settings.pager).hide();
            }
            else
            {
                this.init();
            }
        }

        $.extend(paginga.prototype,
        {
            init: function()
            {
                this.bindEvents();
                this.showPage();

                if(this.settings.history)
                {
                    var plugin = this;

                    if(window.location.hash)
                    {
                        var hash = parseInt(window.location.hash.substring(plugin.settings.historyHashPrefix.length + 1), 10);

                        if(hash <= plugin.totalPages && hash > 0)
                        {
                            plugin.currentPage = hash;
                            plugin.showPage.call(plugin);
                        }
                    }

                    window.addEventListener("popstate", function(event)
                    {
                        plugin.currentPage = event && event.state && event.state.page ? event.state.page : plugin.settings.page;
                        plugin.showPage.call(plugin);
                    });
                }

                this._ready = true;
            },
            bindEvents: function()
            {
                var plugin = this,
                    element = $(plugin.element),
                    previousElement = element.find(plugin.settings.previousPage),
                    nextElement = element.find(plugin.settings.nextPage),
                    firstElement = element.find(plugin.settings.firstPage),
                    lastElement = element.find(plugin.settings.lastPage);

                previousElement.on("click", function()
                {
                    plugin.showPreviousPage.call(plugin);
                });

                nextElement.on("click", function()
                {
                    plugin.showNextPage.call(plugin);
                });

                firstElement.on("click", function()
                {
                    plugin.showFirstPage.call(plugin);
                });

                lastElement.on("click", function()
                {
                    plugin.showLastPage.call(plugin);
                });
            },
            showPreviousPage: function()
            {
                this.currentPage--;
                if(this.currentPage <= 1)
                {
                    this.currentPage = 1;
                    $('.previousPage').css('display', 'none');
                }
                $('#next').removeClass('hidden');
                $('.submitBtn').addClass('hidden');
                this.setHistoryUrl();
                this.showPage();
            },
            showNextPage: function()
            {
                var total_pending_length=CheckDimensionQuestions();

                if (total_pending_length > 0) {
                    return false;
                }
                this.currentPage++;
                if(this.currentPage >= this.totalPages)
                {
                    this.currentPage = this.totalPages;
                    $('.submitBtn').removeClass('hidden');
                    $('#next').addClass('hidden');
                }
                $('html, body').animate({scrollTop:0}, 'slow');
                $('.previousPage').css('display', 'block');
                this.setHistoryUrl();
                this.showPage();
            },
            showFirstPage: function()
            {
                this.currentPage = 1;
                this.setHistoryUrl();
                this.showPage();
            },
            showLastPage: function()
            {
                this.currentPage = this.totalPages;

                this.setHistoryUrl();
                this.showPage();
            },
            showPage: function()
            {
                var firstItem = (this.currentPage * this.settings.itemsPerPage) - this.settings.itemsPerPage,
                    lastItem = firstItem + this.settings.itemsPerPage;

                $.each(this.items, function(index, item)
                {
                    if(index >= firstItem && index < lastItem)
                    {
                        $(item).show();
                        $(item).addClass('visible_data');
                        return true;
                    }
                    $(item).hide();
                    $(item).removeClass('visible_data');
                });
                var plugin = this,
                    element = $(plugin.element),
                    previousElement = element.find(plugin.settings.previousPage),
                    nextElement = element.find(plugin.settings.nextPage),
                    firstElement = element.find(plugin.settings.firstPage),
                    lastElement = element.find(plugin.settings.lastPage);

                if(plugin._ready && plugin.settings.scrollToTop && (element.offset().top - plugin.settings.scrollToTop.offset) < $(window).scrollTop())
                {

                    // $("html, body").animate({ scrollTop: (element.offset().top - plugin.settings.scrollToTop.offset) }, plugin.settings.scrollToTop.speed);

                }

                if(this.currentPage <= 1)
                {
                    previousElement.addClass("disabled");
                    firstElement.addClass("disabled");
                }
                else
                {
                    previousElement.removeClass("disabled");
                    firstElement.removeClass("disabled");
                }

                if(this.currentPage >= this.totalPages)
                {
                    nextElement.addClass("disabled");
                    lastElement.addClass("disabled");
                }
                else
                {
                    nextElement.removeClass("disabled");
                    lastElement.removeClass("disabled");
                }

                var pager = element.find(this.settings.pager),
                    pageNumbers = pager.find(this.settings.pageNumbers);

                if(pageNumbers)
                {
                    pageNumbers.html("");

                    var firstPage = 1;
                    var lastPage = this.totalPages;

                    $('.total-page').text(lastPage);
                    $('.current-page').text(this.currentPage);
                    
                    if(this.settings.maxPageNumbers)
                    {
                        var offset = Math.ceil((this.settings.maxPageNumbers - 1) / 2);

                        firstPage = Math.max(1, this.currentPage - offset);
                        lastPage = Math.min(this.totalPages, this.currentPage + offset);

                        if(lastPage - firstPage < this.settings.maxPageNumbers - 1)
                        {
                            if(firstPage <= offset)
                            {
                                lastPage = Math.min(this.totalPages, firstPage + this.settings.maxPageNumbers - 1);
                            }
                            else if(lastPage > this.totalPages - offset)
                            {
                                firstPage = Math.max(1, lastPage - this.settings.maxPageNumbers + 1);
                            }
                        }
                    }

                    for(var pageNumber = firstPage; pageNumber <= lastPage; pageNumber++)
                    {
                        var className = pageNumber == this.currentPage ? this.settings.currentPageClass : "";

                        pageNumbers.append("<a href='javascript:void(0);' data-page='" + pageNumber + "' class='" + className + "'>" + pageNumber + "</a>");
                    }

                    pageNumbers.find("a").on("click", function()
                    {
                        plugin.currentPage = $(this).data("page");

                        plugin.setHistoryUrl.call(plugin);
                        plugin.showPage.call(plugin);
                    });
                }
            },
            setHistoryUrl: function()
            {
                var plugin = this;

                if(plugin._ready && plugin.settings.history && "pushState" in history)
                {
                    history.pushState({ page: this.currentPage }, null, '#' + plugin.settings.historyHashPrefix + this.currentPage);
                }
            }
        });

        $.fn[pluginName] = function(options)
        {
            return this.each(function()
            {
                if(!$.data(this, "plugin_" + pluginName))
                {
                    $.data(this, "plugin_" + pluginName, new paginga(this, options));
                }
            });
        };

})(jQuery, window, document);

 function CheckDimensionQuestions() {

      var total_question=$('.visible_data').find('.qtn-required').length;
      var question_answerd=$('.visible_data .question-options').find().length;
      var attr = $('.visible_data').find('[data-nottouch=1]').length;
      var liposition = CheckQuestionWithInDimension(0);
      if(liposition.length > 0)
       {
          var height=$('li[data-id="'+liposition[0]+'"]').offset().top-$('.inner-header').outerHeight()-10;
          $('html, body').animate({scrollTop:height}, 'slow');
       }
       
       return liposition.length;

 }

function CheckQuestionWithInDimension(showmsg){
    var liposition=new Array();
    $('.visible_data li').each(function(index, val){

        var check_required=$(this).find('.qtn-required').length;
        var closesinput=$(this).find('.grid-required , .required');
        var total_question_length=$(this).find('.table > tbody tr').length
        var total_answered_question_length=$(this).find('.table > tbody tr input:checked').length

        $.each($(this).find('.table > tbody tr'), function(index, val) {
            var current_field_val=$(this).find('input:checked').val();
            if (current_field_val==undefined) {
              $(this).css('background', '#e3889a');
            }
            else{
              $(this).css('background', '#fff');
            }
        });

        if(check_required>0 && checkerror(closesinput))
        {
            liposition.push($(this).attr('data-id'));
            if(showmsg==0) $(this).find('.message').show();
        }
        else
        {
          $(this).find('.message').hide();
          $(this).find('.question-dimension').css('color', '#31708f');
        }


    });
    return liposition;
}
