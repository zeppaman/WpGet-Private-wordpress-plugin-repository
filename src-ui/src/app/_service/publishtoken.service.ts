import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/toPromise';
import {Message} from 'primeng/components/common/api';
import {MessageService} from 'primeng/components/common/messageservice';


@Injectable()
export class PublishTokenService {


  constructor(private http: Http, private messageService: MessageService) { }

  getList() {
    return this.http.get('/assets/publishtokens.json')
        .toPromise()
        .then(res => <any[]> res.json().rows)
        .then(data => data);
  }

  save(item:any)  {
      console.log(item);
      return this.http.get('/assets/users.json')
      .toPromise()
      .then(res => <any[]> res.json().rows)
      .then(data =>{
         this.messageService.add({severity: 'success', summary: 'User Saved', detail: 'User' + item.username + ' has been saved'
        + JSON.stringify(item)});
         return data[0];
        });
      
  }

  delete(item:any)  {
    console.log(item);
    return this.http.get('/assets/users.json')
    .toPromise()
    .then(res => <any[]> res.json().rows)
    .then(data => { 
      this.messageService.add({severity:'success', summary:'User deleted', detail: 'User' + item.username + ' has been deleted'});
      return data[0];
    });
    
  }

}
