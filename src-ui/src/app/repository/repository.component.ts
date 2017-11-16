import { Component, OnInit, OnChanges, Input } from '@angular/core';
import { RepositoryService } from '../_service/repository.service';
import {Slug} from 'ng2-slugify';

@Component({
  selector: 'app-repository',
  templateUrl: './repository.component.html',
  styleUrls: ['./repository.component.css']
})
export class RepositoryComponent implements OnInit, OnChanges {
    
      displayDialog: boolean;
    
        @Input()
        item: any;
          
          @Input()
          selectedItem: any;
          
          newItem: boolean;
      
          items: any[];
    
      constructor(private itemService: RepositoryService) { }
    
      ngOnInit() {
        this.reload();
     }

     ngOnChanges(changes) {
      console.log(changes);
  }

  nameChange(changes) {
    console.log(changes);
   let  slug = new Slug('german');
    if(this.item.isNew)
    {
      console.log(slug.slugify(changes));
      this.item.reposlug=slug.slugify(changes)
    }

    if( this.item.reposlug.length<10)
    {
      this.item.reposlug= (this.item.reposlug+ "000000000") ;
    }
    this.item.reposlug=this.item.reposlug.substring(0, 7)
  }
     reload()
     {
        this.itemService.getList().then(items => this.items = items);
     }
    
    showDialogToAdd() {
        this.newItem = true;
        this.item = {isNew:true};
        this.displayDialog = true;
    }
    
    closeDialog() {
      this.displayDialog = false;
    }
    save() {
        let items = [...this.items];
        if (this.newItem)
            items.push(this.item);
        else
            items[this.findSelectedCarIndex()] = this.item;
    
        this.itemService.save(this.item).then(item => this.reload());
    
        this.items = items;
        this.item = null;
        this.displayDialog = false;
    }
    
    delete() {
       this.itemService.delete(this.item).then(item => this.reload());
        let index = this.findSelectedCarIndex();
        this.items = this.items.filter((val, i) => i != index);
        this.item = null;    
        this.displayDialog = false;
    }    
    
    onRowSelect(event) {
        this.newItem = false;
        this.item = this.cloneCar(event.data);
        this.displayDialog = true;
    }
    
    cloneCar(c: any): any {
        let item = {};
        for (let prop in c) {
            item[prop] = c[prop];
        }
        return item;
    }
    
    findSelectedCarIndex(): number {
        return this.items.indexOf(this.selectedItem);
    }
    
    }
    
    
    