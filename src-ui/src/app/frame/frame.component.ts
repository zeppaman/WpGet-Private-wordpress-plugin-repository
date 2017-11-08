import { Component, OnInit } from '@angular/core';
import { NgModule }      from '@angular/core';

import {ToolbarModule} from 'primeng/primeng';
import {SidebarModule} from 'primeng/primeng';
import {SplitButtonModule} from 'primeng/primeng';
import {MenuModule, MenuItem} from 'primeng/primeng';
import {MessagesModule} from 'primeng/primeng';
import {MessageModule} from 'primeng/primeng';
import {GrowlModule} from 'primeng/primeng';
import {BreadcrumbModule} from 'primeng/primeng';

@Component({
  selector: 'app-frame',
  templateUrl: './frame.component.html',
  styleUrls: ['./frame.component.css']
})
export class FrameComponent  {

  items: MenuItem[];
  
      ngOnInit() {
          this.items = [
            {label:"Admin",
            items: [
                      {label: 'Users', icon: 'fa-user',  routerLink: "/admin/users"},
                      {label: 'Stores', icon: 'fa-download' , routerLink: "/admin/stores"},
                      {label: 'Tokens', icon: 'fa-refresh', routerLink: "/admin/Tokens"},
            ]},
            {
              label:"User",
            items: [
                      {label: 'Logout', icon: 'fa-refresh', routerLink: "/logout"}
                  ]
                }]
      }
}

