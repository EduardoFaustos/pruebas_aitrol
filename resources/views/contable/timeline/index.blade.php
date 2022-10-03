@extends('contable.tipo_acreedor.base')
@section('action-content')
<style>

.body-wrap {
  background-color: #fff;
  width: 600px;
  min-height: 500px;
  margin: 0 auto;
  font-size: 12px;
}

.pres-timeline {
  font-family: roboto, helvetica, sans-serif;
  font-size: 12px;
  color: #4A4A4A;
  width: 100%;
  margin: 30px 0;
}
.pres-timeline > div > div {
  padding: 1em 0;
  box-sizing: border-box;
}
.pres-timeline .periods-container, .pres-timeline .cards-container {
  overflow: hidden;
  box-sizing: border-box;
  position: relative;
  min-height: 100px;
  transition: height 0.5s ease-in-out;
  background-color: #FFF;
}
.periods-container:before {
  background-image: linear-gradient(left, #FFF, rgba(248, 248, 248, 0));
  left: 0;
  content: "";
  position: absolute;
  z-index: 2;
  top: 0;
  height: 100%;
  width: 100px;
}
.periods-container:after {
  background-image: linear-gradient(right, #FFF, rgba(248, 248, 248, 0));
  right: 0;
  content: "";
  position: absolute;
  z-index: 2;
  top: 0;
  height: 100%;
  width: 100px;
}
.periods-container .btn-back, .periods-container .btn-next {
  display: inline-block;
  width: 15%;
  height: 100%;
  position: absolute;
  cursor: pointer;
  z-index: 10;
  transition: 0.3s ease-in-out;
}
.periods-container .btn-back:hover, .periods-container .btn-next:hover {
  background-color: rgba(0, 0, 0, 0.05);
}
.periods-container .btn-back.hide, .periods-container .btn-next.hide {
  display: none;
}
.periods-container .btn-back {
  left: 0;
}
.periods-container .btn-next {
  right: 0;
}
.periods-container section {
  width: 70%;
  height: 0;
  position: absolute;
  margin-left: 15%;
  border-bottom: 5px solid #dfe3e6;
  padding: 1.5em;
  box-sizing: border-box;
  transition: transform 0.3s ease-in-out, opacity 0.2s ease, height 0.3s ease;
  bottom: 0;
  opacity: 0;
  background-color: #fff;
}
.periods-container section.active {
  height: auto;
  opacity: 1;
  transform: translateX(0);
  z-index: 5;
}
.periods-container section.active .title, .periods-container section.active p {
  display: block;
}
.periods-container section.prev {
  height: auto;
  opacity: 0.4;
  transform: translateX(-100%);
  z-index: 0;
}
.periods-container section.prev .year {
  text-align: right;
}
.periods-container section.next {
  height: auto;
  opacity: 0.4;
  transform: translateX(100%);
  z-index: 0;
}
.periods-container section .year {
  font-size: 20px;
  font-weight: 400;
}
.periods-container section .title {
  color: #4A4A4A;
  font-size: 28px;
  font-weight: 400;
  display: none;
}
.periods-container section p {
  display: none;
}

.timeline-container {
  position: relative;
  width: 100%;
  height: 50px;
  overflow: hidden;
}
.timeline-container:before {
  background-image: linear-gradient(left, #FFF, rgba(248, 248, 248, 0));
  left: 0;
  content: "";
  position: absolute;
  z-index: 2;
  top: 0;
  height: 100%;
  width: 100px;
}
.timeline-container:after {
  background-image: linear-gradient(right, #FFF, rgba(248, 248, 248, 0));
  right: 0;
  content: "";
  position: absolute;
  z-index: 2;
  top: 0;
  height: 100%;
  width: 100px;
}
.timeline-container .timeline {
  position: absolute;
  display: block;
  height: 50px;
  transition: left 0.3s ease-in-out;
}
.timeline-container .timeline ol {
  display: block;
  width: 100%;
  height: 2px;
  background-color: #dfe3e6;
  list-style: none;
  padding-left: 210px;
  padding-right: 300px;
}
.timeline-container .timeline ol li {
  display: inline-block;
  padding: 5px;
  margin-top: -11px;
  margin-left: 80px;
  border-radius: 50%;
  border: 3px solid #7f9298;
  background-color: #FFF;
  position: relative;
  cursor: pointer;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}
.timeline-container .timeline ol li.active {
  box-shadow: none;
}
.timeline-container .timeline ol li.active:before {
  content: "";
  display: block;
  height: 25px;
  width: 1px;
  position: absolute;
  top: -25px;
  transition: opacity 0.3s ease-in-out;
}
.timeline-container .timeline ol li.active:after {
  content: "";
  display: block;
  height: 25px;
  width: 1px;
  position: absolute;
  bottom: -25px;
  transition: opacity 0.3s ease-in-out;
}
.timeline-container .btn-back, .timeline-container .btn-next {
  display: inline-block;
  position: absolute;
  cursor: pointer;
  margin-top: -2px;
  z-index: 11;
  transition: all 0.3s ease;
}
.timeline-container .btn-back.hide, .timeline-container .btn-next.hide {
  display: none;
}
.timeline-container .btn-back:hover, .timeline-container .btn-next:hover {
  border-color: #7f9298;
}
.timeline-container .btn-back {
  left: 1em;
}
.timeline-container .btn-next {
  right: 1em;
}

.cards-container:before {
  background-image: linear-gradient(left, #FFF, rgba(248, 248, 248, 0));
  left: 0;
  content: "";
  position: absolute;
  z-index: 2;
  top: 0;
  height: 100%;
  width: 100px;
}
.cards-container:after {
  background-image: linear-gradient(right, #FFF, rgba(248, 248, 248, 0));
  right: 0;
  content: "";
  position: absolute;
  z-index: 2;
  top: 0;
  height: 100%;
  width: 100px;
}
.cards-container section {
  width: 70%;
  position: absolute;
  margin-left: 15%;
  margin-bottom: 2em;
  border: 1px solid #f5f7f6;
  padding: 1.5em;
  box-sizing: border-box;
  transition: transform 0.3s ease-in-out;
  top: 0;
  opacity: 0;
  border-radius: 8px;
  background-color: #fff;
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.15);
}
.cards-container section.active {
  height: auto;
  opacity: 1;
  transform: translateX(0);
  z-index: 5;
}
.cards-container section.prev {
  height: auto;
  opacity: 0.4;
  transform: translateX(-105%);
  z-index: 0;
}
.cards-container section.next {
  height: auto;
  opacity: 0.4;
  transform: translateX(105%);
  z-index: 0;
}
.cards-container section .year {
  text-align: center;
  font-size: 16px;
  margin: 0;
}
.cards-container section .title {
  font-weight: 400;
}
.cards-container section img {
  width: 100%;
}
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            TIMELINE
        </div>
        <div class="box-body">
            <div class="body-wrap">
                <!-- # component starts -->
                <div class="pres-timeline" id="this-timeline">
                    <!-- ###   -->
                    <!--   <div class="periods-section"> -->
                    <div class="periods-container">
                      @php 
                        $contador=1;
                        //dd($proceso->detalles);
                      @endphp
                     
                      @foreach ($proceso->detalles as $x)
                            @php $title=""; $content=""; $proveedor=""; $secuence=""; @endphp
                            @if($x->tipo=='P')
                              @php
                              $p= Sis_medico\Ct_pedidos_Compra::find($x->id_find);
                              @endphp
                              @if(!is_null($p))
                                @php $title="SOLICITUD DE PEDIDO"; $proveedor= $p->proveedorf->razonsocial; $secuence= $p->secuencia; @endphp
                              @endif
                            @endif
                            @if($x->tipo=='APD')
                                @php
                                $p= Sis_medico\Ct_pedidos_Compra::find($x->id_find);
                                @endphp
                                @if(!is_null($p))
                                  @php $title="PEDIDO APROBADO"; $proveedor= $p->proveedorf->razonsocial; $secuence= $p->secuencia; @endphp
                                @endif
                            @endif
                            @if($x->tipo=='C')
                                @php
                                $p= Sis_medico\Ct_compras::find($x->id_find);
                                @endphp
                                @if(!is_null($p))
                                  @php $title="FACTURA DE COMPRA"; $proveedor= $p->proveedorf->razonsocial; $secuence= $p->numero; @endphp
                                @endif
                            @endif
                            @if($x->tipo=='R')
                                @php
                                $p= Sis_medico\Ct_Retenciones::find($x->id_find);
                                @endphp
                                @if(!is_null($p))
                                  @php $title="RETENCIÓN"; $proveedor= $p->proveedor->razonsocial; $secuence= $p->nro_comprobante; @endphp
                                @endif
                            @endif
                            @if($x->tipo=='P')
                              @php 
                               
                              @endphp   
                              @if($x->module=='EG')
                                @php
                                $p= Sis_medico\Ct_Comprobante_Egreso::find($x->id_find);
                                @endphp
                                @if(!is_null($p))
                                  @php $title="COMPROBANTE DE EGRESO"; $proveedor= $p->proveedor->razonsocial; $secuence= $p->secuencia; @endphp
                                @endif
                              @endif
                            
                            @endif
                        <section class="period-single" period="period{{$contador}}">
                            <h4 class="year">{{date('d/m/Y H:i:s',strtotime($x->created_at))}}</h4>
                            <h2 class="title">{{$title}}</h2>
                          <!--   <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laudantium doloremque, laboriosam officia facere eligendi quam reiciendis, rem explicabo dolores tenetur libero minus, facilis quibusdam. Consectetur amet beatae fuga, architecto magnam.</p> -->
                          {{$proveedor}} - {{$x->secuencia}}
                        </section>
                        @php $contador++ @endphp
                      @endforeach
                      
                       <!-- <section class="period-single" period="period2">
                            <h4 class="year">182x-182x</h4>
                            <h2 class="title">2 Lorem ipsum dolor sit amet, consectetur adipisicing.</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laudantium doloremque, laboriosam officia facere eligendi quam reiciendis, rem explicabo dolores tenetur libero minus, facilis quibusdam. Consectetur amet beatae fuga, architecto magnam.</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laudantium doloremque, laboriosam officia facere eligendi quam reiciendis, rem explicabo dolores tenetur libero minus, facilis quibusdam. Consectetur amet beatae fuga, architecto magnam.</p>
                        </section> -->
                        <!--
                        <section class="period-single" period="period3">
                            <h4 class="year">183x-183x</h4>
                            <h2 class="title">3 Lorem ipsum dolor sit amet, consectetur adipisicing.</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laudantium doloremque, laboriosam officia facere eligendi quam reiciendis, rem explicabo dolores tenetur libero minus, facilis quibusdam. Consectetur amet beatae fuga, architecto magnam.</p>
                        </section> -->
                        <div class="btn-back"></div>
                        <div class="btn-next"></div>
                    </div>
                    <!--   </div> -->
                    <!-- ### -->
                    <!--   <div class="timeline-section"> -->
                    <div class="timeline-container">
                        <!--     # timeline graphic place holder - fill with js -->
                        <div class="timeline"></div>

                        <div class="btn-back"><svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h30v30H0z" />
                                <path fill="#D8D8D8" fill-rule="evenodd" d="M11.828 15l7.89-7.89-2.83-2.828L6.283 14.89l.11.11-.11.11L16.89 25.72l2.828-2.83" />
                            </svg></div>
                        <div class="btn-next"><svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h30v30H0z" />
                                <path fill="#D8D8D8" fill-rule="evenodd" d="M18.172 14.718l-7.89-7.89L13.112 4l10.606 10.607-.11.11.11.11-10.608 10.61-2.828-2.83 7.89-7.89" />
                            </svg></div>
                    </div>
                    <!--   </div> -->
                    <!-- ### -->
                    <!--   <div class="cards-section"> -->
                    <div class="cards-container">
                        @php 
                          $conta=1;
                        @endphp
                        @foreach($proceso->detalles as $x)
                        <section class="card-single active" period="period{{$conta}}">
                            @php $title=""; $content=""; $proveedor=""; $secuence=""; @endphp
                            @if($x->tipo=='P')
                              @php
                              $p= Sis_medico\Ct_pedidos_Compra::find($x->id_find);
                              @endphp
                              @if(!is_null($p))
                                @php $title=$p->fecha; $proveedor= $p->proveedorf->razonsocial; $secuence= $p->secuencia; $content=$p->observacion; @endphp
                              @endif
                            @endif
                            @if($x->tipo=='APD')
                              @php
                              $p= Sis_medico\Ct_pedidos_Compra::find($x->id_find);
                              @endphp
                              @if(!is_null($p))
                                @php $title=$p->fecha; $proveedor= $p->proveedorf->razonsocial; $secuence= $p->secuencia; $content=$p->observacion; @endphp
                              @endif
                            @endif
                            @if($x->tipo=='C')
                             @php
                              $p= Sis_medico\Ct_compras::find($x->id_find);
                              @endphp
                              @php $title=$p->fecha; $proveedor=$p->proveedorf->razonsocial; $secuence=$p->numero; $content=$p->observacion;  @endphp
                            @endif
                            @if($x->tipo=='R')
                             @php
                              $p= Sis_medico\Ct_Retenciones::find($x->id_find);
                              @endphp
                              @php $title=$p->fecha; $proveedor=$p->proveedor->razonsocial; $secuence=$p->nro_comprobante; $content=$p->observacion;  @endphp
                            @endif
                            @if($x->tipo=='P')
                              @php 
                                $title="PAGO COMPLETO";
                              @endphp
                            @endif
                            <h4 class="year">{{$title}}</h4>
                            <h2 class="title">{{$secuence}}</h2>
                            <div class="content">
                               
                                {{$proveedor}} <br/>
                                {{$content}}
                            </div>
                        </section>
                        @php 
                         $conta++;
                        @endphp
                        @endforeach
                    </div>
                    <!--   </div> -->
                    <!-- ###   -->
                </div>
                <!-- # component ends -->
            </div>
        </div>

    </div>
    <script>
        "use strict";
        class PRESTimeline {
            constructor(target, color) {
                // this.__process_stylesheet(document.styleSheets[0]);
                this.base = target;
                this.color = color;
                // console.log(this.color)
                this.periodContainer = $(this.base).find(".periods-container");
                this.cardContainer = $(this.base).find(".cards-container");
                this.timelineNodeContainer = $(this.base).find(".timeline-container .timeline");
                // this.activePeriod = $(this.base).find('.periods-container section.active')
                this._parseData();
                this._initialColor();
                this._generateTimeline();
                this._setStateClasses();
                this._assignBtn();
                this._adjustPeriodContainer();
                this._adjustCardContainer();
                // console.log(this.cardData)
            }
            _parseData() {
                let base = this.base;
                let periods = $(base).find(".periods-container section");
                for (let section of periods) {
                    section.period = $(section).attr("period");
                    section.index = $(section).index();
                }
                // console.log(periods)
                this.periodData = periods;
                let data = $(base).find(".cards-container section");
                // console.log(data)
                for (let section of data) {
                    section.period = $(section).attr("period");
                    section.index = $(section).index();
                }
                // console.log(data)
                this.cardData = data;
                // #assign initial entry point (active items)
                this.activePeriod = this.periodData[0];
                this.activePeriodIndex = 0;
                this.activeCard = this.cardData[0];
                this.activeCardIndex = 0;
            }
            _setStateClasses() {
                // # periods
                $(this.base).find(".periods-container section.active").removeClass("active");
                $(this.base).find(".periods-container section.prev").removeClass("prev");
                $(this.base).find(".periods-container section.next").removeClass("next");
                // console.log("setclass: " + this.activePeriod.index)
                $(this.activePeriod).addClass("active");
                // console.log(this.activePeriod.index)
                // this.activePeriodIndex = this.activePeriod.index
                if ($(this.activePeriod).prev().length != 0) {
                    $(this.activePeriod).prev().addClass("prev");
                    $(this.base).find(".periods-container .btn-back").removeClass("hide");
                }
                else {
                    $(this.base).find(".periods-container .btn-back").addClass("hide");
                }
                if ($(this.activePeriod).next().length != 0) {
                    $(this.activePeriod).next().addClass("next");
                    $(this.base).find(".periods-container .btn-next").removeClass("hide");
                }
                else {
                    $(this.base).find(".periods-container .btn-next").addClass("hide");
                }
                // ## cards
                $(this.base).find(".cards-container section.active").removeClass("active");
                $(this.base).find(".cards-container section.prev").removeClass("prev");
                $(this.base).find(".cards-container section.next").removeClass("next");
                $(this.activeCard).addClass("active");
                // this.activeCardIndex - this.activeCard.index
                if ($(this.activeCard).prev().length != 0) {
                    $(this.activeCard).prev().addClass("prev");
                }
                if ($(this.activeCard).next().length != 0) {
                    $(this.activeCard).next().addClass("next");
                }
                // ## timeline
                $(this.base).find(".timeline li.active").removeClass("active");
                // let findNode = $(this.base).find('.timeline ol li')[this.activeCard.index]
                $(this.timelineData[this.activeCard.index]).addClass("active");
                let timelineB = $(this.base).find(".timeline-container .btn-back");
                let timelineN = $(this.base).find(".timeline-container .btn-next");
                // console.log($(timelineN))
                if (this.activeCardIndex === 0) {
                    timelineB.addClass("hide");
                }
                else {
                    timelineB.removeClass("hide");
                }
                if (this.activeCardIndex >= this.cardData.length - 1) {
                    timelineN.addClass("hide");
                }
                else {
                    timelineN.removeClass("hide");
                }
            }
            // ## timeline generater
            _generateTimeline() {
                // ## create node list
                let htmlWrap = "<ol></ol>";
                $(this.timelineNodeContainer).append(htmlWrap);
                let wrap = $(this.timelineNodeContainer).find("ol");
                let numNode = this.cardData.length;
                for (let i = 0; i < numNode; i++) {
                    let c = this.cardData[i].color;
                    let el = wrap.append('<li class="' +
                        this.cardData[i].period +
                        '" style="border-color: ' +
                        c +
                        '"></li>');
                }
                // ## width of timeline
                let nodeW = 200;
                wrap.css("width", nodeW * numNode - 16);
                let nodeList = $(this.base).find(".timeline ol li");
                this.timelineData = nodeList;
            }
            // ## assign button actions
            _assignBtn() {
                let periodPrev = $(this.base).find(".periods-container .btn-back");
                let periodNext = $(this.base).find(".periods-container .btn-next");
                periodPrev.click(() => {
                    if (this.activePeriodIndex > 0) {
                        // console.log('prev')
                        this.activePeriodIndex -= 1;
                        this.activePeriod = this.periodData[this.activePeriodIndex];
                        this._chainActions("period");
                        this._setStateClasses();
                    }
                    this._adjustPeriodContainer();
                });
                periodNext.click(() => {
                    if (this.activePeriodIndex < this.periodData.length - 1) {
                        // console.log('next' + this.activePeriodIndex)
                        this.activePeriodIndex += 1;
                        this.activePeriod = this.periodData[this.activePeriodIndex];
                        this._chainActions("period");
                        this._setStateClasses();
                    }
                    this._adjustPeriodContainer();
                });
                let timelinePrev = $(this.base).find(".timeline-container .btn-back");
                let timelineNext = $(this.base).find(".timeline-container .btn-next");
                timelinePrev.click(() => {
                    if (this.activeCardIndex > 0) {
                        this.activeCardIndex -= 1;
                        this.activeCard = this.cardData[this.activeCardIndex];
                        this._chainActions("timeline");
                        this._setStateClasses();
                    }
                    this._adjustCardContainer();
                    this._adjustPeriodContainer();
                });
                timelineNext.click(() => {
                    if (this.activeCardIndex < this.cardData.length - 1) {
                        this.activeCardIndex += 1;
                        this.activeCard = this.cardData[this.activeCardIndex];
                        this._chainActions("timeline");
                        this._setStateClasses();
                    }
                    this._adjustCardContainer();
                    this._adjustPeriodContainer();
                });
                // ## assign each timeline li
                for (let i = 0; i < this.timelineData.length; i++) {
                    $(this.timelineData[i]).click(() => {
                        this.activeCardIndex = this.cardData[i].index;
                        this.activeCard = this.cardData[this.activeCardIndex];
                        this._chainActions("timeline");
                        this._setStateClasses();
                        this._adjustCardContainer();
                        this._shiftTimeline();
                    });
                }
            }
            // ## color ##
            _initialColor() {
                for (let i = 0; i < this.periodData.length; i++) {
                    let p = this.periodData[i].period;
                    this.periodData[i].color = this.color[p];
                    let temp = this.periodData[i];
                    $(temp).css("border-color", temp.color);
                    $(temp).find(".year").css("color", temp.color);
                    // ## color for timeline items, this part utilize the period name as class which will be add to the li later
                    // ### cross browser bug fix
                    let sbstyle = document.createElement("style");
                    document.head.appendChild(sbstyle);
                    // let sheet = document.styleSheets[0]
                    sbstyle.sheet.insertRule("li." +
                        p +
                        ".active { background-color: " +
                        this.color[p] +
                        " !important } ", 0);
                    sbstyle.sheet.insertRule("li." + p + "::before { background-color: " + this.color[p] + " } ", 0);
                    sbstyle.sheet.insertRule("li." + p + "::after { background-color: " + this.color[p] + " } ", 0);
                }
                for (let i = 0; i < this.cardData.length; i++) {
                    let p = this.cardData[i].period;
                    this.cardData[i].color = this.color[p];
                    let temp = this.cardData[i];
                    $(temp).css("border-color", temp.color);
                    $(temp).find(".year").css("color", temp.color);
                }
            }
            _adjustPeriodContainer() {
                let activeH = $(this.activePeriod).outerHeight();
                $(this.periodContainer).height(activeH);
                console.log("top adjusted");
            }
            _adjustCardContainer() {
                let activeH = $(this.activeCard).outerHeight() + 24;
                $(this.cardContainer).height(activeH);
                console.log("bot adjusted");
            }
            _shiftTimeline() {
                // #### We need to fix this part if using this component in different sizes ####
                let timelineW = $(this.base).find(".timeline-container").outerWidth();
                let timelinePadding = 210;
                let timelineCenter = 300;
                let liWidth = 16;
                let activeNodeX = $(this.timelineData[this.activeCardIndex]).position().left;
                let finalPos = -activeNodeX + timelinePadding;
                $(this.timelineNodeContainer).css("left", finalPos);
                console.log(activeNodeX);
            }
            _chainActions(state) {
                switch (state) {
                    case "period":
                        console.log("period");
                        if (this.activePeriod.period != this.activeCard.period) {
                            // ## find the closest li with the active period
                            let ta = [];
                            for (let i = 0; i < this.cardData.length; i++) {
                                let temp = this.cardData[i];
                                if (this.activePeriod.period === temp.period)
                                    ta.push(temp);
                            }
                            this.activeCard = ta[0];
                            this.activeCardIndex = ta[0].index;
                        }
                        break;
                    case "timeline":
                        console.log("timeline");
                        if (this.activeCard.period != this.activePeriod.period) {
                            let ta;
                            for (let i = 0; i < this.periodData.length; i++) {
                                let temp = this.periodData[i];
                                if (this.activeCard.period === temp.period)
                                    ta = temp;
                            }
                            this.activePeriod = ta;
                            this.activePeriodIndex = ta.index;
                        }
                        break;
                }
                this._shiftTimeline();
                this._adjustCardContainer();
            }
        }
        // ## document load ##
        $(document).ready(function () {
            let colorcode = {
                period1: "#fec541",
                period2: "#36d484",
                period3: "#32ccf4"
            };
            let timeline = new PRESTimeline($("#this-timeline"), colorcode);
        });
    </script>

</section>
@endsection