import 'package:flutter/material.dart';

class Observer<T>{

  T _obj;
  _BuildableWidget _state;

  set obj(T obj){
    this._obj = obj;
    _state._rebuild();
  }

  Observer([this._obj]);

  void setText(T obj) {
    this._obj = obj;
    _state._rebuild();
  }

}
class StatefulTextWidget extends StatefulWidget{
  final style;
  final Observer<String> observer;

  StatefulTextWidget(this.observer,{this.style});
  @override
  State<StatefulTextWidget> createState() => _StatefulTextWidgetState();

}

class _StatefulTextWidgetState extends State<StatefulTextWidget> implements _BuildableWidget{

  void _rebuild(){
    setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    widget.observer._state = this;
    return SelectableText(
      widget.observer._obj,
      style: widget.style,
    );
  }

}

abstract class _BuildableWidget{
  void _rebuild();
}